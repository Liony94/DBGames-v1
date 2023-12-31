<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @throws NonUniqueResultException
     */
    #[Route('/message', name: 'app_message')]
    public function index(EntityManagerInterface $entityManager, MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        $users = $entityManager->getRepository(User::class)->findAll();

        if (!$user instanceof User) {
            $this->redirectToRoute('app_login');
        }

        $conversations = $entityManager->getRepository(Conversation::class)->findConversationsByUser($user);

        $lastReceivedMessages = [];

        foreach ($conversations as $conversation) {
            $lastReceivedMessages[$conversation->getId()] = $messageRepository->findLastReceivedMessage($conversation, $user);
        }

        $unreadConversations = [];
        foreach ($conversations as $conversation) {
            $unreadMessages = $messageRepository->findBy(['conversation' => $conversation, 'isRead' => false]);
            if (count($unreadMessages) > 0) {
                $unreadConversations[] = $conversation->getId();
            }
        }

        return $this->render('message/main.html.twig', [
            'users' => $users,
            'conversations' => $conversations,
            'lastReceivedMessages' => $lastReceivedMessages,
            'unreadConversations' => $unreadConversations,
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
        $message->setTitle($data['title'] ?? '');
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

    #[Route('/message/reply/{conversationId}', name: 'app_message_reply', methods: ['POST'])]
    public function replyMessage(Request $request, EntityManagerInterface $entityManager, int $conversationId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid JSON data']);
        }

        $conversation = $entityManager->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation instanceof Conversation) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid conversation']);
        }

        $user = $this->getUser();

        $message = new Message();
        $message->setSender($user);
        $message->setContent($data['reply'] ?? '');
        $message->setSentAt(new \DateTime());

        $message->setConversation($conversation);

        $entityManager->persist($message);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Reply sent successfully']);
    }

    #[Route('/message/conversation/{conversationId}/messages', name: 'app_message_conversation_messages', methods: ['GET'])]
    public function getMessages(EntityManagerInterface $entityManager, int $conversationId): JsonResponse
    {
        $conversation = $entityManager->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation instanceof Conversation) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid conversation']);
        }

        $messages = $entityManager->getRepository(Message::class)->findBy(['conversation' => $conversation], ['sentAt' => 'ASC']);

        $messagesData = [];
        foreach ($messages as $message) {
            $messagesData[] = [
                'sender' => $message->getSender()->getUsername(),
                'content' => $message->getContent(),
                'sentAt' => $message->getSentAt()->format('Y-m-d H:i:s'),
            ];
        }

        return new JsonResponse(['status' => 'success', 'messages' => $messagesData]);
    }

    #[Route('/message/conversation/{conversationId}/mark-as-read', name: 'app_message_mark_as_read', methods: ['POST'])]
    public function markAsRead(EntityManagerInterface $entityManager, int $conversationId): JsonResponse
    {
        $conversation = $entityManager->getRepository(Conversation::class)->find($conversationId);
        if (!$conversation instanceof Conversation) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid conversation']);
        }

        $user = $this->getUser();

        $messages = $entityManager->getRepository(Message::class)->findBy(['conversation' => $conversation, 'isRead' => false]);
        foreach ($messages as $message) {
            if ($message->getSender() !== $user) {
                $message->setIsRead(true);
            }
        }

        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Conversation marked as read']);
    }

    #[Route('/conversation/{id}/add-participant', name: 'add_participant', methods: ['POST'])]
    public function addParticipant(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $userId = $request->request->get('user_id');
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $conversation = $entityManager->getRepository(Conversation::class)->find($id);

        if (!$conversation) {
            return new JsonResponse(['status' => 'error', 'message' => 'Conversation not found'], Response::HTTP_NOT_FOUND);
        }

        if ($conversation->getParticipants()->contains($user)) {
            return new JsonResponse(['status' => 'error', 'message' => 'User is already a participant'], Response::HTTP_BAD_REQUEST);
        }

        $conversation->addParticipant($user);
        $entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Participant added'], Response::HTTP_OK);
    }
}


