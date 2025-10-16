<?php

namespace App\Controller;

use App\Service\UserService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/user', name: 'api_user', methods: ['GET', 'POST'])]
class UserController extends AbstractController
{
    private UserService $userService;
    private NormalizerInterface $normalizer;

    public function __construct(UserService $userService, NormalizerInterface $normalizer)
    {
        $this->userService = $userService;
        $this->normalizer = $normalizer;
    }

    #[Route('/update', name: 'update_user', methods: ['PUT'])]
    public function update(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);

        $convertedDob = $user->getDob() ?? null;
        if (isset($data['dob'])) {
            $convertedDob = \DateTime::createFromFormat('Y-m-d', $data['dob']) ?: null;
        }

        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setDob($convertedDob);
        $user->setEmail($data['email']);
        $user->setCpf($data['cpf']);
        $user->setPhoneNumber($data['phoneNumber']);
        $user->setState($data['state']);
        $user->setCity($data['city']);

        $this->userService->add($user);

        return new JsonResponse(['status' => 'User Updated'], 200);
    }

    // #[Route('/getsfdasf', name: 'api_get_userrrr', method: ['GET'])]
    // public function getsss(Request $request): JsonResponse
    // {
    //     // $user = $this->getUser();

    //     // if ($user) {
    //     //     return new JsonResponse(['error' => 'Unauthorized', 401]);
    //     // }

    //     // $data = $this->normalizer->normalize($user, 'json', ['user_all']);
    //     return new JsonResponse(['user' => 'ss'], 200);
    // }
}
