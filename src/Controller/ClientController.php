<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/client', methods: ['POST'])]
class ClientController extends AbstractController
{
    private ClientRepository $clientRepository;
    private NormalizerInterface $normalizer;

    public function __construct(ClientRepository $clientRepository, NormalizerInterface $normalizer)
    {
        $this->clientRepository = $clientRepository;
        $this->normalizer = $normalizer;
    }

    #[Route('/create', name: 'create_client', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['firstName']) || !trim($data['firstName'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['lastName']) || !trim($data['lastName'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['cellphoneNumber']) || !trim($data['cellphoneNumber'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['email']) || !trim($data['email'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['age']) || !trim($data['age'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $client = new Client();
        $client->setFirstName($data['firstName']);
        $client->setLastName($data['lastName']);
        $client->setCellphoneNumber($data['cellphoneNumber']);
        $client->setEmail($data['email']);
        $client->setAge($data['age']);
        $client->setActive(!!$data['active']);
        $client->setCompany($user->getCompany());

        $client = $this->clientRepository->add($client);
        $normalizedData = $this->normalizer->normalize($client, 'json', ['client_all']);

        return new JsonResponse(['status' => 'Client created', 'client' => $normalizedData], 201);
    }

    #[Route('/all', name: 'get_all_clients', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $clients = $this->clientRepository->findBy(['company' => $user->getCompany()], ["id" => "ASC"]);
        $normalizedData = $this->normalizer->normalize($clients, 'json', ['client_all']);

        return new JsonResponse(['clients' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'delete_client', methods: ['DELETE'])]
    public function delete(Client $client): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        // Change to validate client sales
        // $product = $this->productRepository->findOneBy(["client" => $client->getId()]);
        // if ($product) {
        //     throw new UnprocessableEntityHttpException('There are products related to client');
        // }
        $this->clientRepository->delete($client);

        return new JsonResponse(['status' => 'Client Deleted'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'update_client', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['firstName']) || !trim($data['firstName'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['lastName']) || !trim($data['lastName'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['cellphoneNumber']) || !trim($data['cellphoneNumber'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['email']) || !trim($data['email'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        if (!isset($data['age']) || !trim($data['age'])) {
            throw new UnprocessableEntityHttpException('Invalid data format');
        }

        $client = $this->clientRepository->find($id);
        $client->setFirstName($data['firstName']);
        $client->setLastName($data['lastName']);
        $client->setCellphoneNumber($data['cellphoneNumber']);
        $client->setEmail($data['email']);
        $client->setAge($data['age']);
        $client->setActive(!!$data['active']);

        $client = $this->clientRepository->add($client);
        $normalizedData = $this->normalizer->normalize($client, 'json', ['client_all']);

        return new JsonResponse(['status' => 'Client updated', 'client' => $normalizedData], 200);
    }

    #[Route('/{id}', name: 'get_client', methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized', 401]);
        }

        $client = $this->clientRepository->findOneBy(['id' => $id, 'company' => $user->getCompany()]);
        $normalizedData = $this->normalizer->normalize($client, 'json', ['client_all']);

        return new JsonResponse(['client' => $normalizedData], 200);
    }
}
