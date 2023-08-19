<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $users = $entityManager->getRepository(User::class)->findAll();

        if (!$user instanceof User) {
            $this->redirectToRoute('app_login');
        }

        $conversations = $entityManager->getRepository(Conversation::class)->findConversationsByUser($user);

        return $this->render('message/main.html.twig', [
            'users' => $users,
            'conversations' => $conversations,
        ]);
    }

    #[Route('/message/send/{recipientId}', name: 'app_message_send', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $entityManager, int $recipientId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON data']);
        }

        $recipient = $entityManager->getRepository(User::class)->find($recipientId);
        if (!$recipient instanceof User) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid recipient']);
        }

        $user = $this->getUser();

        $message = new Message();
        $message->setSender($user);
        $message->setContent($data['message'] ?? '');
        $message->setSentAt(new \DateTime());

        $conversation = new Conversation();
        if ($user instanceof User) {
            $conversation->addParticipant($user);
        }
        $conversation->addParticipant($recipient);

        $message->setConversation($conversation);

        $entityManager->persist($message);
        $entityManager->persist($conversation);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Message sent successfully']);
    }
}


