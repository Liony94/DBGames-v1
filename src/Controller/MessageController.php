<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\ReplyType;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\MessageRepository;

class MessageController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/message', name: 'app_message')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository,
        ConversationRepository $conversationRepository
    ): Response {
        $users = $userRepository->findAll();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $reply = new Message();
        $replyForm = $this->createForm(ReplyType::class, $reply);
        $replyForm->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipientId = $request->request->get('recipient');
            $recipient = $userRepository->find($recipientId);

            $user = $this->getUser();
            if (!$user instanceof User) {
                throw new \Exception('User must be logged in to send messages.');
            }
            $recipientId = $request->request->get('recipient');
            $recipient = $userRepository->find($recipientId);
            if (!$recipient instanceof User) {
                throw new \Exception('Invalid recipient.');
            }

            $conversation = $conversationRepository->findConversationByUsers($user, $recipient);
            if (!$conversation) {
                $conversation = new Conversation();
                $conversation->setUser1($this->getUser());
                $conversation->setUser2($recipient);
                $entityManager->persist($conversation);
            }

            $message->setRecipient($recipient);
            $message->setSender($this->getUser());
            $message->setCreatedAt(new \DateTime());
            $message->setConversation($conversation);
            $conversation->addMessage($message);
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_message');
        }

        if ($replyForm->isSubmitted() && $replyForm->isValid()) {
            $parentId = $request->request->get('parent_id');
            $parentMessage = $parentId ? $messageRepository->find($parentId) : null;

            if ($parentMessage) {
                $recipient = $parentMessage->getSender();

                $reply->setParentMessage($parentMessage);
                $reply->setRecipient($recipient);
                $reply->setSender($this->getUser());
                $reply->setTitle('Re: ' . $parentMessage->getTitle());
                $reply->setCreatedAt(new \DateTime());
                $reply->setConversation($parentMessage->getConversation());

                $entityManager->persist($reply);
                $entityManager->flush();

                return $this->redirectToRoute('app_message');
            }
        }

        $conversations = $conversationRepository->findBy(['user1' => $this->getUser()]);

        return $this->render('message/main.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'replyForm' => $replyForm->createView(),
            'conversations' => $conversations,
        ]);
    }
}
