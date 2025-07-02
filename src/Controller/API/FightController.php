<?php

namespace App\Controller\API;

use App\Entity\Fight;
use App\Repository\CharacterRepository;
use App\Repository\FightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FightController extends AbstractController{

    //   Force   \\
    private function calculDegats(int $valeur): int {
    // Sécurise la valeur entre 1 et 100
    $valeur = max(1, min(100, $valeur));

    // Moyenne attendue : moitié de la valeur
    $moyenne = $valeur / 2;

    // Ecart type = 1/6e de la valeur, soit une variation possible jusqu’à 1/3 (3 sigma)
    $ecartType = $valeur / 6;

    // Génération gaussienne via Box-Muller
    $u = mt_rand() / mt_getrandmax();
    $v = mt_rand() / mt_getrandmax();
    $z = sqrt(-2.0 * log($u)) * cos(2.0 * pi() * $v); // Variable gaussienne (moyenne = 0, écart-type = 1)

    // Appliquer moyenne et écart-type
    $degats = $moyenne + $z * $ecartType;

    // Arrondir et s’assurer que les dégâts sont >= 0
    return max(0, round($degats));
}


    //   Vitesse   \\
    private function doubleAction(int $diff): bool {
        $absDiff = abs($diff);

        // Si la différence est nulle, aucune chance de jouer deux fois
        if ($absDiff == 0) return false;

        // Probabilité entre 0 et 1 (capée à 100% si différence > 100)
        $proba = min($absDiff, 100) / 100;

        // Génère un nombre aléatoire entre 0 et 1
        $rand = mt_rand(0, 100) / 100;

        // Retourne true si l'utilisateur doit jouer deux fois
        return $rand <= $proba;
    }

    
    #[IsGranted('ROLE_USER')]
    #[Route('/api/fight/start', name: 'api_fight_start', methods: ['POST'])]
    public function start(Request $request, EntityManagerInterface $entityManager, CharacterRepository $characterRepository): JsonResponse
    {
        $user = $this->getUser();
        $data = $request->request;

        $selectedCharacter = $characterRepository->find($data->get('selectedCharacter'));
        $selectedOpponent = $characterRepository->find($data->get('selectedOpponent'));
        if (!$selectedCharacter || !$selectedOpponent) {
            return $this->json([
                'message' => 'Character or opponent not found'
            ], 404);
        }

        // Vérification d'un combat existant entre ces deux personnages pour cet utilisateur
        $existingFight = $entityManager->getRepository(Fight::class)->findOneBy([
            'myCharacter' => $selectedCharacter,
            'opponentCharacter' => $selectedOpponent,
            'createdBy' => $user // à condition d'avoir ce champ dans l'entité Fight
        ]);

        if ($existingFight) {
            return $this->json([
                'game' => $existingFight->getId(),
                'message' => 'A fight between these two characters already exists for this user',
                'success' => false
            ], 409);
        }

        if ($user !== $selectedCharacter->getUser() && !in_array('ROLE_ADMIN', $user->getRoles())) return $this->json([
            'message' => 'You are not allowed to fight with this character'
        ], 403);

        if ($selectedCharacter->getId() === $selectedOpponent->getId()) {
            return $this->json([
                'message' => 'You cannot fight with the same character'
            ], 400);
        }

        if ($selectedCharacter->getSpeed() < $selectedOpponent->getSpeed()) {
            $firstPlayer = false;
        } elseif ($selectedCharacter->getSpeed() > $selectedOpponent->getSpeed()) {
            $firstPlayer = true;
        } else {
            // If speeds are equal, randomly choose the first player
            $firstPlayer = rand(0, 1) ? false : true;
        }

        if ($firstPlayer) {
            $this->doubleAction($selectedCharacter->getSpeed() - $selectedOpponent->getSpeed());
        } else {
            $this->doubleAction($selectedOpponent->getSpeed() - $selectedCharacter->getSpeed());
        }

        $fight = new Fight();
        $fight->setMyCharacter($selectedCharacter);
        $fight->setOpponentCharacter($selectedOpponent);
        $fight->setIsMyTurn($firstPlayer);
        $fight->setCreatedBy($user);

        $entityManager->persist($fight);
        $entityManager->flush();

        return $this->json([
            'game' => $fight->getId(),
            'firstPlayer' => $firstPlayer,
            'success' => true
        ]);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('/api/fight/attack', name: 'api_fight_attack', methods: ['POST'])]
    public function attack(Request $request, EntityManagerInterface $entityManager, FightRepository $fightRepository, CharacterRepository $characterRepository): JsonResponse
    {
        $user = $this->getUser();
        $data = $request->request;

        $fight = $fightRepository->find($data->get('game'));
        if (!$fight) {
            return $this->json([
                'message' => 'Game not found'
            ], 404);
        }


        $selectedCharacter = $fight->getMyCharacter();
        $selectedOpponent = $fight->getOpponentCharacter();
        if (!$selectedCharacter || !$selectedOpponent) {
            return $this->json([
                'message' => 'Character or opponent not found'
            ], 404);
        }

        if ($user !== $selectedCharacter->getUser() && !in_array('ROLE_ADMIN', $user->getRoles())) return $this->json([
            'message' => 'You are not allowed to fight with this character'
        ], 403);

        if ($selectedCharacter->getId() === $selectedOpponent->getId()) {
            return $this->json([
                'message' => 'You cannot fight with the same character'
            ], 400);
        }
        
        $fight = $entityManager->getRepository(Fight::class)->findOneBy([
            'myCharacter' => $selectedCharacter,
            'opponentCharacter' => $selectedOpponent,
            'createdBy' => $user
        ]);

        return $this->json([
            'degats' => $this->calculDegats($selectedCharacter->getStrength()),
            'success' => true
        ]);


    }
}
