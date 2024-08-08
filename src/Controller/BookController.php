<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Enum\Status;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\BorrowRepository;
use App\Entity\Borrow;
use App\Entity\Book;
use App\Service\UserService;
use App\Service\BookService;
use App\Service\BorrowService;

class BookController extends AbstractController
{
    private BookService $bookService;
    private UserService $userService;
    private BorrowService $borrowService;
    private BorrowRepository $borrowRepository;

    public function __construct(BookService $bookService,UserService $userService,BorrowService $borrowService,BorrowRepository $borrowRepository)
    {
        $this->bookService=$bookService;
        $this->userService=$userService;
        $this->borrowService=$borrowService;
        $this->borrowRepository=$borrowRepository;
    }

    #[Route('/book',methods:['POST'], name: 'add_book')]
    public function addBook(Request $request): JsonResponse
    {
       $data = json_decode($request->getContent(),true);
        $publishedDate = \DateTime::createFromFormat('Y-m-d', $data['publisheddate']);
            if (!$publishedDate) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Invalid date format. Expected format: Y-m-d'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
            // dd($publishedDate);
       if(isset($data['title']) && isset($data['author']) && isset($data['isbn']) && isset($data['publisheddate']))
       {
       $book=new Book();
       $book->setTitle($data['title']);
       $book->setAuthor($data['author']);
       $book->setIsbn($data['isbn']);
       $book->setPublishedDate($publishedDate);
       $book->setCreatedAt(new \DateTimeImmutable('now'));
       $book->setUpdatedAt(new \DateTimeImmutable('now'));
       // Convert string to Status enum
       try {
        $status = Status::from($data['status']);
    } catch (\ValueError $e) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Invalid status value'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
    $book->setStatus($status);
       $duplBook=$this->bookService->checkDuplBook($data['isbn']);
       if($duplBook)
       {
        return new JsonResponse([
            'status'=>true,
            'message'=>'Book Already Exist!!!'
        ],JsonResponse::HTTP_CREATED);
       }
       try{
        $this->bookService->addBook($book);
        return new JsonResponse([
            'status'=>'Success',
            'message'=>'Book Added Successfully!!!'
        ],JsonResponse::HTTP_CREATED);
       } 
       catch(\Exception $e)
       {
            return new JsonResponse([
                'status'=>'error',
                'message'=>$e->getMessage()
            ],JsonResponse::HTTP_BAD_REQUEST);
       }
    }else{
        return new JsonResponse([
            'status'=>'400',
            'message'=>'Please Input all data'
        ]);
    }
    }
    // Update Book
    #[Route('/book/update/{id}', methods: ['PUT'], name: 'update_book')]
    public function updateBook(Request $request,int $id): JsonResponse
    {
       $data = json_decode($request->getContent(),true);
       $book = $this->bookService->getBookById($id);
        if (!$book) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Book not found'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
            
       if (!isset($data['title'], $data['author'], $data['isbn'], $data['publisheddate'], $data['status'])) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Please input all data'
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
        $publishedDate = \DateTime::createFromFormat('Y-m-d', $data['publisheddate']);
        if (!$publishedDate) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid date format. Expected format: Y-m-d'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setIsbn($data['isbn']);
        $book->setPublishedDate($publishedDate);
        $book->setUpdatedAt(new \DateTimeImmutable('now'));

        // Convert string to Status enum
        try {
            $status = Status::from($data['status']);
        } catch (\ValueError $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid status value'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $book->setStatus($status);

        try {
            $this->bookService->updateBook($book);
            return new JsonResponse([
                'status' => 'success',
                'message' => 'Book updated successfully!'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/book',methods:["GET"],name:'list_book')]
    public function listBook(): JsonResponse
    {
        $books = $this->bookService->listBooks();
        if($books)
        {
        $responseData = array_map(function($books){
            return[
                'id'=>$books->getId(),
                'title'=>$books->getTitle(),
                'author'=>$books->getAuthor(),
                'isbn'=>$books->getIsbn(),
                'PublishedDate'=>$books->getPublishedDate()->format('Y-m-d'),
                'status'=>$books->getStatus()->value
            ];
        },$books);
        return $this->json(
                ['status'=>'success',
                'data'=>$responseData]);
    }
    return $this->json(['status'=>'Book not Found'],Response::HTTP_OK);
    }
    // Get Book by Id
    #[Route('/book/{id}/',methods:['GET'])]
    public function getBookById(int $id): JsonResponse
    {
        $book = $this->bookService->getBookById($id);

        if(!$book)
        {
            return $this->json(['status'=>'Book not Found'],Response::HTTP_NOT_FOUND);
        }
        $responseData=[
            'status' => 'success',
            'data' => [
            'id'=>$book->getId(),
            'title'=>$book->getTitle(),
            'author'=>$book->getAuthor(),
            'isbn'=>$book->getIsbn(),
            'PublishedDate'=>$book->getPublishedDate()->format('Y-m-d'),
            'status'=>$book->getStatus()->value
            ]
        ];
        return $this->json($responseData);
    }
    #[Route('/book/{id}',methods:['DELETE'])]
    public function deleteBook(int $id): JsonResponse
    {
        $res = $this->bookService->deleteBook($id);

        if($res)
        {
            return $this->json([
                'status'=>"success",
                "message"=>"Book deleted Successfully"
            ]);
        }else
        {
            return $this->json([
                'status' => 'error',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Book not found'
                ]
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    // Borrow Book
    #[Route('/borrow',methods:['PUT'])]
    public function borrowWhenAvailable(Request $request): JsonResponse
    {
        $data=json_decode($request->getContent(),true);

        $book=$this->bookService->getBookById($data['bookid']);
        
        // print_r();die;
        if(!$book)
        {
            return new JsonResponse([
                'status' => 'error',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Book not found'
                ]
            ],JsonResponse::HTTP_NOT_FOUND);
        }
        $user=$this->userService->getUserById($data['userid']);
        if(!$user)
        {
            return $this->json([
                'status' => 'error',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'User not found'
                ]
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        if($book->getStatus()->value=="borrowed")
        {
            return $this->json([
                'status' => 'error',
                'error' => [
                    'code' => '200',
                    'message' => 'Book Already Borrowed!!'
                ]
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        $status = Status::from("borrowed");
        $book->setStatus($status);
        $book_stat=$this->bookService->updateBookStatus($book);
        if($book_stat)
        {
            $user = $this->userService->getUserById($data['userid']);
            $book = $this->bookService->getBookById($data['bookid']);
            $borrow=new Borrow();
            $borrow->setUserid($user);
            $borrow->setBookid($book);
            $borrow->setBorrowDate((new \DateTimeImmutable('now')));
            $check=$this->borrowService->borrowBook($borrow);
            if($check)
            {
                return new JsonResponse([
                    "status"=>"Book Borrowed Successfully"
                ], JsonResponse::HTTP_CREATED);
            }
        }
    }
    //Return Book
    // #[Route('/borrow/return/{id}',methods:['PUT'])]
    // public function returnBook(int $id): JsonResponse
    // {
    //     // Fetch the borrowed book details
    //     $borrowBook = $this->borrowService->checkBorrowBook($id);
    //     print_r($borrowBook->getReturnDate());die;

    //     // if($borrowBook)
    //     // {
    //     //     $borrow->setReturnDate(new \DateTimeImmutable('now'));
    //     //     $updateReturnBook=$this->borrowService->updateReturnDate($borrow);
    //     //     if($updateReturnBook)
    //     //     {
    //     //         // $updateReturnBook=$this->bookService->changeStatus($borrow);
    //     //     }
    //         return new JsonResponse([
    //             "status"=>"Not Found",
    //             "message"=>$borrowBook
    //         ],JsonResponse::HTTP_NOT_FOUND);
    //     //     // print_r($borrow);die;
    //     //     // return true;
    //     // }
    //     // else
    //     // {
    //     //     return new JsonResponse([
    //     //         "status"=>"Not Found",
    //     //         "message"=>"Book Not Borrowed"
    //     //     ],JsonResponse::HTTP_NOT_FOUND);
    //     // }
    //     // print_r($book);die;
        
    // }
    #[Route('/borrow/return/{id}', methods: ['POST'], name: 'return_book')]
    public function returnBook(int $id): JsonResponse
    {
        $borrow = $this->borrowRepository->find($id);

        if (!$borrow) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Borrow record not found'
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->borrowService->returnBook($borrow);
        $getBookId=$this->bookService->getBookById($borrow->getBookid()->getId());
        $getBookId->setStatus(Status::from('available'));
        $changeStatus=$this->bookService->changeBookStatus($getBookId);
        
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Book returned successfully'
        ]);
    }

}
