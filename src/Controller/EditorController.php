<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Repository\EditorRepository;
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

final class EditorController extends AbstractController
{
    // Liste des éditeurs

    #[Route('/api/editors', name: 'app_editors', methods: ['GET'])]
    #[OA\Response(
        response:200,
        description:'Retourne la liste des éditeurs',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Editor::class, groups: ['editor:read'], name: 'EditorRead')
        ))
    )]
    #[OA\Tag('Editeurs', 'Routes concernant la gestion des éditeurs de jeu vidéo')]
    public function getEditors(EditorRepository $editorRepository, Request $request): JsonResponse
    {   
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $editors = $editorRepository->findAllWithPagination($page, $limit);

        return $this->json($editors, Response::HTTP_OK, [], ['groups' => 'editor:read']);
    }

    // Récupération d'un éditeur 

    #[Route('api/editor/{id}', name:'app_editor', methods:['GET'])]
    #[OA\Response(
        response:200,
        description:"retourne l'éditeur choisi, modifié ou crée",
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Editor::class, groups: ['editor:read'], name: 'EditorRead')
        ))
    )]
    #[OA\Tag('Editeurs')]
    public function getEditor(Editor $editor): JsonResponse
    {
        return $this->json($editor, Response::HTTP_OK, [], ['groups' => 'editor:read']);
    }

    // Création d'un éditeur

    #[Route('/api/v1/editor', name: 'app_new_editor', methods: ['POST'])]
    #[OA\Response(
        response:201,
        description:"retourne l'éditeur qui vient d'être créer",
        content: new OA\JsonContent(
            ref: new Model(type: Editor::class, groups: ['editor:write'], name: 'EditorWrite')
        )
    )]
    #[OA\RequestBody(
        content: new Model(type: Editor::class, groups: ['editor:write'], name: 'EditorWrite')
    )]
    #[OA\Tag('Editeurs')]
    public function createEditor(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $editor = $serializer->deserialize($request->getContent(), type:Editor::class, format:'json');
        $em->persist($editor);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_editor', ['id' => $editor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($editor, Response::HTTP_CREATED, ["Location" => $location], ['groups' => 'editor:write']);
    }

    // Modification d'un éditeur

    #[Route('/api/v1/editor/{id}', name: 'app_edit_editor', methods: ['PUT'])]
    #[OA\Response(
        response:200,
        description:"retourne l'éditeur qui vient d'être modifié",
        content: new OA\JsonContent(
            ref: new Model(type: Editor::class, groups: ['editor:write'], name: 'EditorWrite')
        )

    )]
    #[OA\RequestBody(
        content: new Model(type: Editor::class, groups: ['editor:write'], name: 'EditorWrite')
    )]
    #[OA\Tag('Editeurs')]
    public function updateEditor(Editor $currentEditor, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedEditor = $serializer->deserialize($request->getContent(), Editor::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEditor]);
        $em->flush();

        $location = $urlGenerator->generate(
            'app_editor', ['id' => $updatedEditor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($updatedEditor, Response::HTTP_OK, ["Location" => $location]);
    }

    // Suppression d'un éditeur 

    #[Route('api/v1/editor/{id}/delete', name:'app_delete_editor', methods:['DELETE'])]
    #[OA\Response(
        response:200,
        description: 'retourne un message de succès',
    )]
    #[OA\Tag('Editeurs')]
    public function deleteEditor(Editor $editor, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($editor);
        $em->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }
    
}
