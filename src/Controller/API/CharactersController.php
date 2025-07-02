<?php

namespace App\Controller\API;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use BcMath\Number;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class CharactersController extends AbstractController{

    // Test route
    #[Route('/characters', name: 'app_characters')]
    public function index(CharacterRepository $characterRepository): Response
    {
        $characters = $characterRepository->findAll();
        return $this->render('characters/index.html.twig', [
            'controller_name' => 'CharactersController',
            'characters' => $characters
        ]);
    }

    
    #[Route('/api/characters', name: 'api_characters', methods: ['GET'])]
    public function characters(CharacterRepository $characterRepository): JsonResponse
    {
        $characters = $characterRepository->findAll();
        $charactersArray = [];

        foreach ($characters as $character) {
            $charactersArray[] = [
                'id' => $character->getId(),
                'name' =>  $character->getName(),
                'image' => $character->getImage(),
                'life' => $character->getLife(),
                'speed' => $character->getSpeed(),
                'strength' => $character->getStrength(),
                'regeneration' => $character->getRegeneration(),
                'resistance' => $character->getResistance(),
                'endurance' => $character->getEndurance(),
                'critical' => $character->getCritical(),
                'user' => $character->getUser()->getId(),
            ];
        }

        return $this->json([
            'characters' => $charactersArray,
            'success' => true
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/api/my/characters', name: 'api_my_characters', methods: ['GET'])]
    public function myCharacters(CharacterRepository $characterRepository): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['message' => 'User not authenticated', 'success' => false], 401);
        }
        $characters = $characterRepository->findBy(['user' => $user]);
        $charactersArray = [];

        foreach ($characters as $character) {
            $charactersArray[] = [
                'id' => $character->getId(),
                'name' =>  $character->getName(),
                'image' => $character->getImage(),
                'life' => $character->getLife(),
                'speed' => $character->getSpeed(),
                'strength' => $character->getStrength(),
                'regeneration' => $character->getRegeneration(),
                'resistance' => $character->getResistance(),
                'endurance' => $character->getEndurance(),
                'critical' => $character->getCritical(),
                'user' => $character->getUser()->getId(),
            ];
        }

        return $this->json([
            'characters' => $charactersArray,
            'success' => true
        ]);
    }
    
    #[Route('/api/character/{id}', name: 'api_character', methods: ['GET'])]
    public function character(string $id, CharacterRepository $characterRepository): JsonResponse
    {
        $character = $characterRepository->find($id);
        return $this->json([
            'character' => [
                'id' => $character->getId(),
                'name' =>  $character->getName(),
                'image' => $character->getImage(),
                'life' => $character->getLife(),
                'speed' => $character->getSpeed(),
                'strength' => $character->getStrength(),
                'regeneration' => $character->getRegeneration(),
                'resistance' => $character->getResistance(),
                'endurance' => $character->getEndurance(),
                'critical' => $character->getCritical(),
                'user' => $character->getUser()->getId(),
            ],
            'success' => true
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/api/create/character', name: 'api_create_character', methods: ['POST'])]
    public function createCharacter(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): JsonResponse
    {
        $user = $this->getUser();
        $data = $request->request;

        $character = new Character();
        $character->setName($data->get('name'));
        $character->setLife($data->get('life'));
        $character->setSpeed($data->get('speed'));
        $character->setStrength($data->get('strength'));
        $character->setRegeneration($data->get('regeneration'));
        $character->setResistance($data->get('resistance'));
        $character->setEndurance($data->get('endurance'));
        $character->setCritical($data->get('critical'));
        $character->setUser($user);

        $image = $request->files->get('image');
        $imagesDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/characters';
        if ($image) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            if (!file_exists($imagesDirectory)) {
                mkdir($imagesDirectory, 0777, true);
            }

            // Move the file to the directory where images are stored
            try {
                $image->move($imagesDirectory, $newFilename);
            } catch (FileException $e) {
                return $this->json([
                    'message' => 'An error occurred while uploading the image',
                    'success' => false
                ], 500);
            }
            $character->setImage($newFilename);
        }

        $entityManager->persist($character);
        $entityManager->flush();

        return $this->json([
            'character' => [
                'id' => $character->getId(),
                'name' =>  $character->getName(),
                'image' => $character->getImage(),
                'life' => $character->getLife(),
                'speed' => $character->getSpeed(),
                'strength' => $character->getStrength(),
                'regeneration' => $character->getRegeneration(),
                'resistance' => $character->getResistance(),
                'endurance' => $character->getEndurance(),
                'critical' => $character->getCritical(),
                'user' => $character->getUser()->getId(),
            ],
            'success' => true
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/api/edit/character/{id}', name: 'api_edit_character', methods: ['POST', 'PATCH'])]
    public function editCharacter(Request $request, string $id, CharacterRepository $characterRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): JsonResponse
    {
        $user = $this->getUser();
        $character = $characterRepository->find($id);
        if (!$character) return $this->json([
            'message' => 'Character not found'
        ], 404);
        if ($user !== $character->getUser() && !in_array('ROLE_ADMIN', $user->getRoles())) return $this->json([
            'message' => 'You are not allowed to edit this character'
        ], 403);

        $data = $request->request;

        $character->setName($data->get('name'));
        $character->setLife($data->get('life'));
        $character->setSpeed($data->get('speed'));
        $character->setStrength($data->get('strength'));
        $character->setRegeneration($data->get('regeneration'));
        $character->setResistance($data->get('resistance'));
        $character->setEndurance($data->get('endurance'));
        $character->setCritical($data->get('critical'));
        $character->setUser($user);;

        $image = $request->files->get('image');
        $imagesDirectory = $this->getParameter('kernel.project_dir').'/public/uploads/characters';
        if ($image) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

            if (!file_exists($imagesDirectory)) {
                mkdir($imagesDirectory, 0777, true);
            }

            // Move the file to the directory where images are stored
            try {
                $image->move($imagesDirectory, $newFilename);
            } catch (FileException $e) {
                return $this->json([
                    'message' => 'An error occurred while uploading the image',
                    'success' => false
                ], 500);
            }
            $character->setImage($newFilename);
        }

        $entityManager->persist($character);
        $entityManager->flush();

        return $this->json([
            'character' => [
                'id' => $character->getId(),
                'name' =>  $character->getName(),
                'image' => $character->getImage(),
                'life' => $character->getLife(),
                'speed' => $character->getSpeed(),
                'strength' => $character->getStrength(),
                'regeneration' => $character->getRegeneration(),
                'resistance' => $character->getResistance(),
                'endurance' => $character->getEndurance(),
                'critical' => $character->getCritical(),
                'user' => $character->getUser()->getId(),
            ],
            'success' => true
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/api/delete/character/{id}', name: 'api_delete_character', methods: ['POST', 'DELETE'])]
    public function deleteCharacter(string $id, CharacterRepository $characterRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        $character = $characterRepository->find($id);
        if (!$character) return $this->json([
            'message' => 'Character not found'
        ], 404);
        if ($user !== $character->getUser() && !in_array('ROLE_ADMIN', $user->getRoles())) return $this->json([
            'message' => 'You are not allowed to edit this character'
        ], 403);

        $entityManager->remove($character);
        $entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('/api/characters/random/{number}', name: 'api_random_character', methods: ['GET'])]
    public function randomCharacter(int $number, CharacterRepository $characterRepository): JsonResponse
    {
        $user = $this->getUser();
    
        if (!$user) {
            return $this->json(['message' => 'User not authenticated', 'success' => false], 401);
        }
    
        $queryBuilder = $characterRepository->createQueryBuilder('c');
        $queryBuilder->where('c.user != :user')
            ->setParameter('user', $user)
            ->setMaxResults($number);
    
        $characters = $queryBuilder->getQuery()->getResult();
    
        $charactersArray = [];
        foreach ($characters as $character) {
            $charactersArray[] = [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'image' => $character->getImage(),
                'life' => $character->getLife(),
                'speed' => $character->getSpeed(),
                'strength' => $character->getStrength(),
                'regeneration' => $character->getRegeneration(),
                'resistance' => $character->getResistance(),
                'endurance' => $character->getEndurance(),
                'critical' => $character->getCritical(),
                'user' => $character->getUser()->getId(),
            ];
        }
    
        return $this->json([
            'characters' => $charactersArray,
            'success' => true
        ]);
    }
    
}
