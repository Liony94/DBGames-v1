<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FriendsController extends AbstractController
{
    #[Route(path: '/user/friends', name: 'app_user_friends', methods: ['GET'])]
    public function displayFriends(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        $friends = $user->getFriends();

        return $this->render('friends/friends.html.twig', [
            'friends' => $friends,
        ]);
    }

    #[Route(path: '/user/friends/{username}', name: 'app_user_friends_add', methods: ['POST'])]
    public function addFriend(EntityManagerInterface $entityManager, UserRepository $userRepository, string $username): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();
        if ($user->getUsername() === $username) {
            throw new \InvalidArgumentException('Vous ne pouvez pas vous ajouter vous-même comme ami.');
        }

        $friend = $userRepository->findOneBy(['username' => $username]);
        if (!$friend) {
            throw $this->createNotFoundException('L\'utilisateur demandé n\'existe pas.');
        }

        $user->addFriend($friend);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Demande d\'ami envoyée avec succès.');
    }

    #[Route(path: '/user/search', name: 'app_user_search', methods: ['GET'])]
    public function searchUsers(Request $request, UserRepository $userRepository): Response
    {
        $query = $request->query->get('query');
        $users = $userRepository->searchUsersByName($query);
        $usersData = [];

        foreach ($users as $user) {
            $usersData[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername()
            ];
        }

        return $this->json($usersData);
    }
}
