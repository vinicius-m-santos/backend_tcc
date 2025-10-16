<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/register', methods: ['POST'])]
class RegisterController
{
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            throw new UnprocessableEntityHttpException("Invalid data format");
        }

        if (!isset($data["firstName"]) || !trim($data["firstName"])) {
            throw new UnprocessableEntityHttpException("Invalid data format");
        }

        if (!isset($data["lastName"]) || !trim($data["lastName"])) {
            throw new UnprocessableEntityHttpException("Invalid data format");
        }

        if (!isset($data["email"]) || !trim($data["email"])) {
            throw new UnprocessableEntityHttpException("Invalid data format");
        }

        if (!isset($data["password"]) || !trim($data["password"])) {
            throw new UnprocessableEntityHttpException("Invalid data format");
        }

        if (!isset($data["companyName"]) || !trim($data["companyName"])) {
            throw new UnprocessableEntityHttpException("Invalid data format");
        }

        $user = new User();
        $user->setFirstName($data["firstName"]);
        $user->setLastName($data["lastName"]);
        $user->setEmail($data["email"]);
        $user->setPassword($hasher->hashPassword($user, $data["password"]));

        $em->persist($user);

        $company = new Company();
        $company->setName($data["companyName"]);
        $company->setActive(true);
        $company->addUser($user);

        $em->persist($company);

        $user->setCompany($company);
        $em->persist($user);

        $em->flush();

        return new JsonResponse(['status' => 'User created'], 201);
    }
}
