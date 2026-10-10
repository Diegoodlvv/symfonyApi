<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
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

final class CategoryController extends AbstractController
{
    // Liste des catégories

    #[Route('/api/categories', name: 'app_categories', methods: ['GET'])]
    #[OA\Response(
        response:200,
        description:'Retourne la liste des catégories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class, groups: ['category:read'])
        ))
    )]
    #[OA\Tag('Catégories', 'Routes concernant la gestion des catégories de jeu vidéo')]
    public function index(categoryRepository $categoryRepository): JsonResponse
    {   
        $categories = $categoryRepository->findAll();

        return $this->json($categories, Response::HTTP_OK, [], ['groups' => 'category:read']);
    }

    // Récupération d'un éditeur 

    #[Route('api/category/{id}', name:'app_category', methods:['GET'])]
    #[OA\Response(
        response:200,
        description:"retourne la catégorie choisie, modifiée ou créee",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class, groups: ['category:read'])
        ))
    )]
    #[OA\Tag('Catégories')]
    public function getCategory(Category $category): JsonResponse
    {
        return $this->json($category, Response::HTTP_OK, [], ['groups' => 'category:read']);
    }

    // Création d'un éditeur

    #[Route('/api/v1/category', name: 'app_new_category', methods: ['POST'])]
    #[OA\Response(
        response:201,
        description:"retourne la catégorie qui vient d'être créee",
        content: new OA\JsonContent(
            ref: new Model(type: Category::class, groups: ['category:write'])
        )
    )]
    #[OA\RequestBody(
        content: new Model(type: Category::class, groups: ['category:write'])
    )]
    #[OA\Tag('Catégories')]
    public function createCategory(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), type:Category::class, format:'json');
        $em->persist($category);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_category', ['id' => $category->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($category, Response::HTTP_CREATED, ["Location" => $location], ['groups' => 'category:write']);
    }

    // Modification d'un éditeur

    #[Route('/api/v1/category/{id}', name: 'app_edit_category', methods: ['PUT'])]
    #[OA\Response(
        response:200,
        description:"retourne la catégorie qui vient d'être modifiée",
        content: new OA\JsonContent(
            ref: new Model(type: Category::class, groups: ['category:write'])
        )

    )]
    #[OA\RequestBody(
        content: new Model(type: Category::class, groups: ['category:write'])
    )]
    #[OA\Tag('Catégories')]
    public function updateEditor(Category $currentCategory, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedCategory = $serializer->deserialize($request->getContent(), Category::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCategory]);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_category', ['id' => $updatedCategory->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($updatedCategory, Response::HTTP_OK, ["Location" => $location]);
    }

    // Suppression d'un éditeur 

    #[Route('api/v1/category/{id}/delete', name:'app_delete_category', methods:['DELETE'])]
    #[OA\Response(
        response:200,
        description: 'retourne un message de succès',
    )]
    #[OA\Tag('Catégories')]
    public function deleteCategory(Category $category, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($category);
        $em->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }
    
}
