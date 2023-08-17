<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class FriendRequestService
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function getUnreadRequestsCount(): int
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return 0;
        }

        $receivedFriendRequests = $user->getReceivedFriendRequests();
        $unreadRequestsCount = 0;
        foreach ($receivedFriendRequests as $friendRequest) {
            if (!$friendRequest->getAccepted()) {
                $unreadRequestsCount++;
            }
        }

        return $unreadRequestsCount;
    }

}
