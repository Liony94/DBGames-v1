<?php

namespace App\Controller;

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

        $formUser = $this->createForm(UsernameProfileType::class, $user);
        $formDescription = $this->createForm(UserDescriptionProfileType::class, $user);
        $formGames = $this->createForm(UserGamesProfileType::class, $user);
        $formCity = $this->createForm(UserCityProfileType::class, $user);

        if ($this->handleForm($formUser, $request, $entityManager) || $this->handleForm($formDescription, $request, $entityManager) || $this->handleForm($formGames, $request, $entityManager) || $this->handleForm($formCity, $request, $entityManager)) {
            return $this->redirectToRoute('app_user_profile');
        }

        $avatarUrl = $this->getAvatarUrl($user->getUsername());

        return $this->render('user_profile/profile.html.twig', [
            'formUser' => $formUser->createView(),
            'formDescription' => $formDescription->createView(),
            'formCity' => $formCity->createView(),
            'formGames' => $formGames->createView(),
            'avatarUrl' => $avatarUrl,
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

    private function getAvatarUrl(string $username): string
    {
        return "https://avatars.dicebear.com/api/human/$username.svg";
    }
}
