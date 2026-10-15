<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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

final class UserController extends AbstractController
{
    // Liste des utilisateurs

    #[Route('/api/users', name: 'app_users', methods: ['GET'])]
    #[OA\Response(
        response:200,
        description:'Retourne la liste des utilisateurs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['user:read'], name: 'UserRead')
        ))
    )]
    #[OA\Tag('Utilisateurs', 'Routes concernant la gestion des utilisateurs')]
    public function getUsers(UserRepository $userRepository, Request $request): JsonResponse
    {   
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $users = $userRepository->findAllWithPagination($page, $limit);

        return $this->json($users, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    // Récupération d'un utilisateur

    #[Route('api/users/{id}', name:'app_user', methods:['GET'])]
    #[OA\Response(
        response:200,
        description:"retourne l'utilisateur choisi, modifié ou créé",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['user:read'], name: 'UserRead')
        ))
    )]
    #[OA\Tag('Utilisateurs')]
    public function getUserById(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'user:read']);
    }

    // Création d'un utilisateur

    #[Route('/api/v1/users', name: 'app_new_user', methods: ['POST'])]
    #[OA\Response(
        response:201,
        description:"retourne l'utilisateur qui vient d'être créé",
        content: new OA\JsonContent(
            ref: new Model(type: User::class, groups: ['user:write'], name: 'UserWrite')
        )
    )]
    #[OA\RequestBody(
        content: new Model(type: User::class, groups: ['user:write'], name: 'UserWrite')
    )]
    #[OA\Tag('Utilisateurs')]
    public function createUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), type:User::class, format:'json');
        $em->persist($user);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_user', ['id' => $user->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($user, Response::HTTP_CREATED, ["Location" => $location], ['groups' => 'user:write']);
    }

    // Modification d'un utilisateur

    #[Route('/api/v1/users/{id}', name: 'app_edit_user', methods: ['PUT'])]
    #[OA\Response(
        response:200,
        description:"retourne l'utilisateur qui vient d'être modifié",
        content: new OA\JsonContent(
            ref: new Model(type: User::class, groups: ['user:write'], name: 'UserWrite')
        )

    )]
    #[OA\RequestBody(
        content: new Model(type: User::class, groups: ['user:write'], name: 'UserWrite')
    )]
    #[OA\Tag('Utilisateurs')]
    public function updateUser(User $currentUser, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_user', ['id' => $updatedUser->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($updatedUser, Response::HTTP_OK, ["Location" => $location], ['groups' => 'user:write']);
    }

    // Modification de l'abonnement à la newsletter d'un utilisateur

    #[OA\Response(
        response:200,
        description:"retourne l'utilisateur qui vient d'être modifié",
        content: new OA\JsonContent(
            ref: new Model(type: User::class, groups: ['user:newsletter'], name: 'UserSubscription')
        )

    )]
    #[OA\RequestBody(
        content: new Model(type: User::class, groups: ['user:newsletter'], name: 'UserSubscription')
    )]
    #[OA\Tag('Utilisateurs')]
    #[Route('/api/v1/users/{id}/subscription', name: 'app_edit_user_subscription', methods: ['PATCH'])]
    public function updateSubscriptionToNewsletter(User $currentUser, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedUser = $serializer->deserialize($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_user', ['id' => $updatedUser->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($updatedUser, Response::HTTP_OK, ["Location" => $location], ['groups' => 'user:newsletter']);
    }

    // Suppression d'un utilisateur

    #[Route('api/v1/users/{id}/delete', name:'app_delete_user', methods:['DELETE'])]
    #[OA\Response(
        response:200,
        description: 'retourne un message de succès',
    )]
    #[OA\Tag('Utilisateurs')]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user );
        $em->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }
    
}


