<?php

namespace App\Controller;

use App\Entity\VideoGame;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class VideoGameController extends AbstractController
{
    // Liste des jeux vidéo

    #[Route('/api/videogames', name: 'app_videogames', methods: ['GET'])]
    #[OA\Response(
        response:200,
        description:'Retourne la liste des jeux vidéo',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VideoGame::class, groups: ['videogame:read'], name: 'VideoGameRead')
        ))
    )]
    #[OA\Tag('Jeux Vidéo', 'Routes concernant la gestion des jeux vidéo')]
    public function getVideoGames(VideoGameRepository $videoGameRepository, Request $request): JsonResponse
    {   
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $videoGames = $videoGameRepository->findAllWithPagination($page, $limit);

        return $this->json($videoGames, Response::HTTP_OK, [], ['groups' => 'videogame:read']);
    }

    // Récupération d'un éditeur 

    #[Route('api/videogames/{id}', name:'app_videogame', methods:['GET'])]
    #[OA\Response(
        response:200,
        description:"retourne le jeu vidéo choisi, modifié ou créé",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VideoGame::class, groups: ['videogame:read'], name: 'VideoGameRead')
        ))
    )]
    #[OA\Tag('Jeux Vidéo')]
    public function getVideoGame(VideoGame $videoGame): JsonResponse
    {
        return $this->json($videoGame, Response::HTTP_OK, [], ['groups' => 'videogame:read']);
    }

    // Création d'un éditeur

    #[Route('/api/v1/videogames', name: 'app_new_videogame', methods: ['POST'])]
    #[OA\Response(
        response:201,
        description:"retourne le jeu vidéo qui vient d'être créé",
        content: new OA\JsonContent(
            ref: new Model(type: VideoGame::class, groups: ['videogame:write'], name: 'VideoGameWrite')
        )
    )]
    #[OA\RequestBody(
        content: new Model(type: VideoGame::class, groups: ['videogame:write'], name: 'VideoGameWrite')
    )]
    #[OA\Tag('Jeux Vidéo')]
    public function createVideoGame(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $videoGame = $serializer->deserialize($request->getContent(), type:VideoGame::class, format:'json');
        $em->persist($videoGame);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_videogame', ['id' => $videoGame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($videoGame, Response::HTTP_CREATED, ["Location" => $location], ['groups' => 'videogame:write']);
    }

    // Modification d'un éditeur

    #[Route('/api/v1/videogames/{id}', name: 'app_edit_videogame', methods: ['PUT'])]
    #[OA\Response(
        response:200,
        description:"retourne le jeu vidéo qui vient d'être modifié",
        content: new OA\JsonContent(
            ref: new Model(type: VideoGame::class, groups: ['videogame:write'], name: 'VideoGameWrite')
        )

    )]
    #[OA\RequestBody(
        content: new Model(type: VideoGame::class, groups: ['videogame:write'], name: 'VideoGameWrite')
    )]
    #[OA\Tag('Jeux Vidéo')]
    public function updateVideoGame(VideoGame $currentVideoGame, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedVideoGame = $serializer->deserialize($request->getContent(), VideoGame::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentVideoGame]);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_videogame', ['id' => $updatedVideoGame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($updatedVideoGame, Response::HTTP_OK, ["Location" => $location]);
    }

    // Suppression d'un jeu vidéo

    #[Route('api/v1/videogames/{id}/delete', name:'app_delete_videogame', methods:['DELETE'])]
    #[OA\Response(
        response:200,
        description: 'retourne un message de succès',
    )]
    #[OA\Tag('Jeux Vidéo')]
    public function deleteVideoGame(VideoGame $videoGame, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($videoGame);
        $em->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }
    
}
