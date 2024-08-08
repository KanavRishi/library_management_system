<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[Route('/user', methods: ['POST'], name: 'create_user')]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['email'], $data['role'], $data['password'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Please provide all required data'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));
        $user->setCreatedAt(new \DateTimeImmutable('now'));
        $user->setUpdatedAt(new \DateTimeImmutable('now'));

       $duplUser=$this->userService->checkDuplUser($data['email']);
    //    print_r($duplUser);die;
       if($duplUser)
       {
            return new JsonResponse([
                'status'=>true,
                'message'=>'User Already Exist!!!'
            ],JsonResponse::HTTP_CREATED);
       }

        try {
            $role = Role::from($data['role']);
        } catch (\ValueError $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid Role value'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $user->setRole($role);
        try {
            $this->userService->createUser($user);
            return new JsonResponse([
                'status' => 'success',
                'message' => 'User created successfully'
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/user/{id}',methods:["PUT"])]
    public function updateUser(Request $request,int $id): JsonResponse
    {
        $data=json_decode($request->getContent(),true);
        $user=$this->userService->getUserById($id,$data);
        if(!$user)
        {
            return new JsonResponse([
                "status"=>"User Not Found",
            ],JsonResponse::HTTP_NOT_FOUND);
        }
        if (!isset($data['name'], $data['email'], $data['role'], $data['password'])) {
            return new JsonResponse([
                'status'=>'error',
                'message'=>'Please Input all values'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT));

        try {
            $role = Role::from($data['role']);
        } catch (\ValueError $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid role value'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }
        $user->setRole($role);
        try{
            $this->userService->updateUser($user);
            return new JsonResponse([
                "status"=>"success",
                "message"=>"User Updated Successfully"
            ],JsonResponse::HTTP_CREATED);
        }catch(\Exception $e){
            return new JsonResponse([
                "status"=>"error",
                "message"=>$e->getMessage()
            ],JsonResponse::HTTP_BAD_REQUEST);
        }
    }   

    #[Route('/user',methods:["GET"],name:'list_user')]
    public function listUser(): JsonResponse
    {
        $users = $this->userService->listusers();
        if($users)
        {
        $responseData = array_map(function($users){
            return[
                'id'=>$users->getId(),
                'name'=>$users->getName(),
                'email'=>$users->getEmail(),
                'password'=>$users->getPassword(),
                'role'=>$users->getRole()->value
            ];
        },$users);
        return $this->json(
                ['status'=>'success',
                'data'=>$responseData]);
    }
    return $this->json(['status'=>'User not Found'],JsonResponse::HTTP_NOT_FOUND);
    }
    #[Route('/user/{id}/',methods:['GET'])]
    public function getUserById(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if(!$user)
        {
            return $this->json(['status'=>'User not Found'],JsonResponse::HTTP_NOT_FOUND);
        }
        $responseData=[
            'status' => 'success',
            'data' => [
            'id'=>$user->getId(),
            'name'=>$user->getName(),
            'email'=>$user->getEmail(),
            'password'=>$user->getPassword(),
            'role'=>$user->getRole()->value
            ]
        ];
        return $this->json($responseData);
    }
    #[Route('/user/{id}',methods:['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        $res = $this->userService->deleteUser($id);

        if($res)
        {
            return $this->json([
                'status'=>"success",
                "message"=>"User deleted Successfully"
            ]);
        }else
        {
            return $this->json([
                'status' => 'error',
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'User not found'
                ]
            ], JsonResponse::HTTP_NOT_FOUND);
        }
    }
    

}
