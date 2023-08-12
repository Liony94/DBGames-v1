<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserLolSearchController extends AbstractController
{
    #[Route('/user/lol/search', name: 'app_user_lol_search')]
    public function index(): Response
    {
        return $this->render('user_lol_search/index.html.twig', [
            'controller_name' => 'UserLolSearchController',
        ]);
    }
}
