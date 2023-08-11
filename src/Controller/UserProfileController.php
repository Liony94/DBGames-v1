<?php

namespace App\Controller;

use App\Form\UserDescriptionProfileType;
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
            return $this->redirectToRoute('app_login');
        }

        $formUser = $this->createForm(UsernameProfileType::class, $user);

        $formUser->handleRequest($request);
        if ($formUser->isSubmitted() && $formUser->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['message' => 'Mis à jour avec succès!']);
            }

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_user_profile');
        }

        $formDescription = $this->createForm(UserDescriptionProfileType::class, $user);

        $formDescription->handleRequest($request);
        if ($formDescription->isSubmitted() && $formDescription->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['message' => 'Mis à jour avec succès!']);
            }

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_user_profile');
        }

        $username = $user->getUsername();
        $avatarUrl = "https://avatars.dicebear.com/api/human/$username.svg";

        return $this->render('user_profile/profile.html.twig', [
            'formUser' => $formUser->createView(),
            'formDescription' => $formDescription->createView(),
            'avatarUrl' => $avatarUrl,
        ]);
    }
}
