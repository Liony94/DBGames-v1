<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\MessageRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\FriendRequestService;
use Symfony\Component\HttpFoundation\Response;

class HeaderController extends AbstractController
{
    private $friendRequestService;
    private $messageRequestService;

    public function __construct(FriendRequestService $friendRequestService, MessageRequestService $messageRequestService)
    {
        $this->friendRequestService = $friendRequestService;
        $this->messageRequestService = $messageRequestService;
    }

    public function renderHeader(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->render('partials/header.html.twig', [
                'unreadRequestsCount' => 0,
                'unreadMessagesCount' => 0
            ]);
        }

        $unreadMessagesCount = $this->messageRequestService->countUnreadMessages($user);
        $unreadRequestsCount = $this->friendRequestService->getUnreadRequestsCount();

        return $this->render('partials/header.html.twig', [
            'unreadRequestsCount' => $unreadRequestsCount,
            'unreadMessagesCount' => $unreadMessagesCount
        ]);
    }
}
