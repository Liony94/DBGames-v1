<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
//    #[Route('/register', name: 'app_register')]
//    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
//    {
//        $user = new User();
//        $form = $this->createForm(RegistrationFormType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $user->setPassword(
//                $userPasswordHasher->hashPassword(
//                    $user,
//                    $form->get('plainPassword')->getData()
//                )
//            );
//
//            $entityManager->persist($user);
//            $entityManager->flush();
//
//            return $userAuthenticator->authenticateUser(
//                $user,
//                $authenticator,
//                $request
//            );
//        }
//
//        return $this->render('registration/register.html.twig', [
//            'registrationForm' => $form->createView(),
//        ]);
//    }

    // same class but return json response
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username'], $data['password'], $data['email'], $data['city'], $data['description'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setCity($data['city']);
        $user->setDescription($data['description']);
        $user->setPassword($userPasswordHasher->hashPassword($user, $data['password']));

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }
}
