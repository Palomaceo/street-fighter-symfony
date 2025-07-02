<?php
// dans le dossier src/Controller/API/

namespace App\Controller\API;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($this->getUser()) {
            return new JsonResponse(['message' => 'User already logged in'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPseudo($data['pseudo']);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $data['password']
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        // Optionally, send a confirmation email here

        return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
    }
}