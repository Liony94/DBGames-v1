<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\MessageRepository;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, MessageRepository $messageRepository): Response {

        $users = $userRepository->findAll();

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipientId = $request->request->get('recipient');
            $recipient = $userRepository->find($recipientId);

            $message->setRecipient($recipient);
            $message->setSender($this->getUser());
            $message->setCreatedAt(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_message');
        }

        $messages = $messageRepository->findBy(['recipient' => $this->getUser()]);

        return $this->render('message/main.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'messages' => $messages,
        ]);
    }
}
