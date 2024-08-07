<?php
    namespace App\Service;

    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Borrow;
    use App\Repository\BorrowRepository;
    use App\Enum\Status;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class BorrowService
    {
        private $entityManager;
        private $validator;
        private BorrowRepository $borrowRepository;

        public function __construct(EntityManagerInterface $entityManager,ValidatorInterface $validator,BorrowRepository $borrowRepository)
        {
            $this->entityManager=$entityManager;
            $this->validator=$validator;
            $this->borrowRepository=$borrowRepository;
        }
        public function borrowBook(Borrow $borrow): bool
        {
            $this->entityManager->persist($borrow);
            $this->entityManager->flush();
            return true;
        }
        // public function checkBorrowBook(int $id): ?Borrow
        // {
        //     // return $this->entityManager->getRepository(Borrow::class)->find($id);
        //     return $this->borrowRepository->find('5');
        // }
        // public function updateReturnDate(Borrow $borrow): bool
        // {
        //     $this->entityManager->persist($borrow);
        //     $this->entityManager->flush();
        //     return true;
        // }
        // public function getBookidByid(int $id): ?Borrow
        // {
        //     return $this->borrowRepository->find($id);
        // }
        public function returnBook(Borrow $borrow): Borrow
        {
            $borrow->setReturnDate((new \DateTimeImmutable('now')));
    
            $this->entityManager->persist($borrow);
            $this->entityManager->flush();
    
            return $borrow;
        }
        public function getBorrowHistory(): array
        {
            return $this->borrowRepository->findAll();
        }
    }