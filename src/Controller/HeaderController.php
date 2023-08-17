<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\FriendRequestService;

class HeaderController extends AbstractController
{
    private $friendRequestService;

    public function __construct(FriendRequestService $friendRequestService)
    {
        $this->friendRequestService = $friendRequestService;
    }

    public function renderHeader()
    {
        $unreadRequestsCount = $this->friendRequestService->getUnreadRequestsCount();
        return $this->render('partials/header.html.twig', [
            'unreadRequestsCount' => $unreadRequestsCount
        ]);
    }
}
