<?php
// src/Service/UserService.php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    public function createUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
    public function updateUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }
    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }
    public function listUsers(): array
    {
        return $this->userRepository->findAll();
    }
    public function deleteUser(int $id): bool
    {
        $user=$this->userRepository->find($id);

        if(!$user)
        {
            return false;
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return true;
    }
    public function checkDuplUser(string $email): bool
    {
        $check_dupl= $this->userRepository->findOneBy(['email'=>$email]);
        if($check_dupl)
        {
            return true;
        }
        return false;
    }
}


?>