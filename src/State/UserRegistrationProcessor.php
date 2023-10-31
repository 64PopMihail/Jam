<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Dto\UserRegistrationDto;
use App\Exception\EmailAlreadyExistsException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationProcessor implements ProcessorInterface
{
    private UserPasswordHasherInterface $userPasswordHasher;

    private EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
    )
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        $duplicate = $this->entityManager->getRepository(User::class)->findBy(["email" =>$data->email]);
        if (!empty($duplicate)) {
            throw new EmailAlreadyExistsException();
        }
        $user = new User();
        $user->setEmail(strtolower($data->email));
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $data->plainPassword,
            )
        );
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}