<?php
// src/EventListener/JWTSuccessHandler.php
namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JWTSuccessHandler
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $company = $user->getCompany();
        $companyData = $this->normalizer->normalize($company, 'json', ['groups' => 'company_only']);

        $data['user'] = [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getUserIdentifier(),
            'cpf' => $user->getCpf(),
            'phoneNumber' => $user->getPhoneNumber(),
            'state' => $user->getState(),
            'city' => $user->getCity(),
            'dob' => $user->getDob(),
            'roles' => $user->getRoles(),
            'company' => $companyData
        ];

        $event->setData($data);
    }
}
