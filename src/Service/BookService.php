<?php
    namespace App\Service;

    use Doctrine\ORM\EntityManagerInterface;
    use App\Entity\Book;
    use App\Repository\BookRepository;
    use App\Enum\Status;
    use Symfony\Component\Validator\Validator\ValidatorInterface;

    class BookService
    {
        private $entityManager;
        private $validator;
        private BookRepository $bookRepository;

    public function __construct(EntityManagerInterface $entityManager,ValidatorInterface $validator,BookRepository $bookRepository)
    {
        $this->entityManager=$entityManager;
        $this->validator=$validator;
        $this->bookRepository=$bookRepository;
    }

    public function addBook(Book $book): void
    {
        $error=$this->validator->validate($book);
        if(count($error)>0)
        {
            throw new \Exception((string)$error);
        }
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }

    public function listBooks(): array
    {
        return $this->bookRepository->findAll();
    }

    public function getBookById(int $id): ?Book
    {
        return $this->bookRepository->find($id);
    }
    public function deleteBook(int $id): bool
    {
        $book=$this->bookRepository->find($id);

        if(!$book)
        {
            return false;
        }
        $this->entityManager->remove($book);
        $this->entityManager->flush();
        return true;
    }
    public function updateBook(Book $book): void
    {
        // print_r($book);die;
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
    public function updateBookStatus(Book $book): bool
    {
        // print_r($book);die;
        $this->entityManager->persist($book);
        $this->entityManager->flush();
        return true;
    }
    public function checkDuplBook(int $isbn): bool
    {
        $check_dupl= $this->bookRepository->findByOne(['isbn'=>$isbn]);
        if($check_dupl)
        {
            return true;
        }
        return false;
    }
    // public function borrowWhenAvailable($id): bool
    // {

    // }
    }
?>