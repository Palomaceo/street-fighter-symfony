<?php

namespace App\Controller\API;

use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'api', methods: ['GET'])]
    public function api(): JsonResponse
    {
        return $this->json([
            'message' => 'API Street Fighter',
            'success' => true
        ]);
    }

    // Temporary route to test the fight system
    #[Route('/api/fight/{id1}/vs/{id2}', name: 'api_versus_fight', methods: ['GET'])]
    public function fight(string $id1, string $id2, CharacterRepository $characterRepository): JsonResponse
    {
        $character1 = $characterRepository->find($id1);
        $character2 = $characterRepository->find($id2);
        if (!$character1) return $this->json([
            'message' => 'Character 1 not found'
        ], 404);
        if (!$character2) return $this->json([
            'message' => 'Character 2 not found'
        ], 404);

        $pointsCharacter1 = 0;
        $pointsCharacter2 = 0;
        if ($character1->getStrength() > $character2->getStrength()) {
            $pointsCharacter1++;
        } else if ($character1->getStrength() < $character2->getStrength()) {
            $pointsCharacter2++;
        }
        if ($character1->getSpeed() > $character2->getSpeed()) {
            $pointsCharacter1++;
        } else if ($character1->getSpeed() < $character2->getSpeed()) {
            $pointsCharacter2++;
        }
        if ($character1->getDurability() > $character2->getDurability()) {
            $pointsCharacter1++;
        } else if ($character1->getDurability() < $character2->getDurability()) {
            $pointsCharacter2++;
        }
        if ($character1->getPower() > $character2->getPower()) {
            $pointsCharacter1++;
        } else if ($character1->getPower() < $character2->getPower()) {
            $pointsCharacter2++;
        }
        if ($character1->getCombat() > $character2->getCombat()) {
            $pointsCharacter1++;
        } else if ($character1->getCombat() < $character2->getCombat()) {
            $pointsCharacter2++;
        }

        if ($pointsCharacter1 > $pointsCharacter2) {
            $winner = $character1;
            $loser = $character2;
        } else if ($pointsCharacter1 < $pointsCharacter2) {
            $winner = $character2;
            $loser = $character1;
        } else {
            $statsCharacter1 = $character1->getStrength() + $character1->getSpeed() + $character1->getDurability() + $character1->getPower() + $character1->getCombat();
            $statsCharacter2 = $character2->getStrength() + $character2->getSpeed() + $character2->getDurability() + $character2->getPower() + $character2->getCombat();
            if ($statsCharacter1 > $statsCharacter2) {
                $winner = $character1;
                $loser = $character2;
            } else if ($statsCharacter1 < $statsCharacter2) {
                $winner = $character2;
                $loser = $character1;
            } else {
                if (random_int(0, 1) === 0) {
                    $winner = $character1;
                    $loser = $character2;
                } else {
                    $winner = $character2;
                    $loser = $character1;
                }
            }
        }

        return $this->json([
            'winner' => [
                'id' => $winner->getId(),
                'name' =>  $winner->getName(),
                'image' => $winner->getImage(),
                'life' => $winner->getLife(),
                'speed' => $winner->getSpeed(),
                'strength' => $winner->getStrength(),
                'regeneration' => $winner->getRegeneration(),
                'resistance' => $winner->getResistance(),
                'endurance' => $winner->getEndurance(),
                'critical' => $winner->getCritical(),
                'user' => $winner->getUser()->getId(),
            ],
            'loser' => [
                'id' => $loser->getId(),
                'name' =>  $loser->getName(),
                'image' => $loser->getImage(),
                'life' => $loser->getLife(),
                'speed' => $loser->getSpeed(),
                'strength' => $loser->getStrength(),
                'regeneration' => $loser->getRegeneration(),
                'resistance' => $loser->getResistance(),
                'endurance' => $loser->getEndurance(),
                'critical' => $loser->getCritical(),
                'user' => $loser->getUser()->getId(),
            ],
            'success' => true
        ]);
    }

}