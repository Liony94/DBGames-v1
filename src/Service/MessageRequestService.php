<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MessageRequestService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function countUnreadMessages(User $user): int
    {
        $conversations = $this->entityManager->getRepository(Conversation::class)->findConversationsByUser($user);
        $unreadMessagesCount = 0;
        foreach ($conversations as $conversation) {
            $unreadMessagesCount += $conversation->getUnreadCount();
        }

        return $unreadMessagesCount;
    }
}
