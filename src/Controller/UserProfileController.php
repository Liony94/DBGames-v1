<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\User;
use App\Form\UserCityProfileType;
use App\Form\UserDescriptionProfileType;
use App\Form\UserGamesProfileType;
use App\Form\UsernameProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserProfileController extends AbstractController
{
    #[Route('/user/profile', name: 'app_user_profile', methods: ["GET", "POST"])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToLogin();
        }

        $numberOfFriends = count($user->getFriends());
        $numberOfMessages = count($user->getMessages());

        $receivedFriendRequests = $user->getReceivedFriendRequests();
        $unreadRequestsCount = 0;
        foreach ($receivedFriendRequests as $friendRequest) {
            if (!$friendRequest->getAccepted()) {
                $unreadRequestsCount++;
            }
        }

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $formUser = $this->createForm(UsernameProfileType::class, $user);
        $formDescription = $this->createForm(UserDescriptionProfileType::class, $user);
        $formGames = $this->createForm(UserGamesProfileType::class, $user);
        $formCity = $this->createForm(UserCityProfileType::class, $user);

        if ($this->handleForm($formUser, $request, $entityManager) || $this->handleForm($formDescription, $request, $entityManager) || $this->handleForm($formGames, $request, $entityManager) || $this->handleForm($formCity, $request, $entityManager)) {
            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user_profile/profile.html.twig', [
            'formUser' => $formUser->createView(),
            'formDescription' => $formDescription->createView(),
            'formCity' => $formCity->createView(),
            'formGames' => $formGames->createView(),
            'numberOfFriends' => $numberOfFriends,
            'unreadRequestsCount' => $unreadRequestsCount,
            'numberOfMessages' => $numberOfMessages
        ]);
    }

    #[Route('/user/profile/{id}', name: 'app_user_profile_id', methods: ["GET"])]
    public function showProfile($id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return $this->redirectToLogin();
        }

        $currentUser = $this->getUser();

        if (!$currentUser instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $areFriends = $this->areFriends($currentUser, $user);
        $requestSent = $this->requestSent($currentUser, $user);

        $numberOfFriends = count($user->getFriends());
        $numberOfMessages = count($user->getMessages());

        return $this->render('user_profile/profileId.html.twig', [
            'user' => $user,
            'areFriends' => $areFriends,
            'requestSent' => $requestSent,
            'numberOfFriends' => $numberOfFriends,
            'numberOfMessages' => $numberOfMessages
        ]);
    }

    private function redirectToLogin(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    private function handleForm($form, Request $request, EntityManagerInterface $entityManager): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($this->getUser());
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['message' => 'Mis à jour avec succès!']);
            }

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return true;
        }
        return false;
    }
    private function areFriends(User $user1, User $user2): bool
    {
        return $user1->getFriends()->contains($user2) || $user2->getFriends()->contains($user1);
    }

    private function requestSent(User $sender, User $receiver): bool
    {
        foreach ($sender->getSentFriendRequests() as $request) {
            if ($request->getReceiver() === $receiver) {
                return true;
            }
        }

        foreach ($receiver->getReceivedFriendRequests() as $request) {
            if ($request->getSender() === $sender) {
                return true;
            }
        }

        return false;
    }


}
