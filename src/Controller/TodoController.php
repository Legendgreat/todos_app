<?php

namespace App\Controller;

use App\DTO\TodoDTO;
use App\DTO\TodoUpdateDTO;
use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

final class TodoController extends AbstractController
{
    #[Route('/todo', name: 'read_all', methods: ["GET"])]
    #[OA\Response(
        response: 200,
        description: 'Returns all todos',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Todo::class))
        )
    )]
    #[OA\Tag(name: 'todos')]
    public function read_all(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $todos = $entityManager->getRepository(Todo::class)->findAll();
        return new JsonResponse($serializer->serialize($todos, "json"));
    }

    #[Route('/todo', name: 'create', methods: ["POST"], format: 'json')]
    #[OA\Response(
        response: 200,
        description: 'Returns the created todo',
        content: new Model(type: Todo::class)
    )]
    #[OA\Tag(name: 'todos')]
    public function create(
        #[MapRequestPayload] TodoDTO $todoDto,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        if (!$todoDto) {
            throw new BadRequestException('Unsupported content format.');
        }
        $todo = new Todo();
        $todo->setTitle($todoDto->title);
        $todo->setDescription($todoDto->description);
        $todo->setFinished($todoDto->finished);
        $errors = $validator->validate($todo);
        if (count($errors) > 0) {
            return new JsonResponse($errors);
        }
        $entityManager->persist($todo);
        $entityManager->flush();

        return new JsonResponse($serializer->serialize($todo, "json"));
    }

    #[Route('/todo/{id}', name: 'read', methods: ["GET"])]
    public function read(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        int $id
    ): JsonResponse {
        $todo = $entityManager->getRepository(Todo::class)->find($id);
        if (!$todo) {
            throw new ResourceNotFoundException('Todo with id: ' . $id . ' not found.');
        }
        return new JsonResponse($serializer->serialize($todo, "json"));
    }

    #[Route('/todo/{id}', name: 'update', methods: ["PUT"], format: 'json')]
    public function update(
        #[MapRequestPayload] TodoUpdateDTO $todoDto,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $todo = $entityManager->getRepository(Todo::class)->find($id);
        if (!$todo) {
            throw new ResourceNotFoundException('Todo with id: ' . $id . ' not found.');
        }
        if (!$todoDto) {
            throw new BadRequestException('Unsupported content format.');
        }
        $todo->setTitleIfNotNull($todoDto->title);
        $todo->setDescriptionIfNotNull($todoDto->description);
        $todo->setFinishedIfNotNull($todoDto->finished);
        $errors = $validator->validate($todo);
        if (count($errors) > 0) {
            return new JsonResponse($errors);
        }
        $entityManager->persist($todo);
        $entityManager->flush();

        return new JsonResponse($serializer->serialize($todo, "json"));
    }

    #[Route('/todo/{id}', name: 'delete', methods: ["DELETE"])]
    public function delete(
        EntityManagerInterface $entityManager,
        int $id
    ): JsonResponse {
        $todo = $entityManager->getRepository(Todo::class)->find($id);
        if (!$todo) {
            throw new ResourceNotFoundException('Todo with id: ' . $id . ' not found.');
        }
        $entityManager->remove($todo);
        $entityManager->flush();

        return new JsonResponse("Todo with id: " . $id . " succesfully removed.");
    }
}
