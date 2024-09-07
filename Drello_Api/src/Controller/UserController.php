<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/register', name: 'register',methods:["Post"])]
    public function new(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response{
        $data = json_decode($request->getContent(), true); 
        $name = $data['name'] ?? null;
        $username = $data['username'] ?? null;
        $email = $data['email']?? null;
        $password = $data['password']?? null;
        $profilePic = $data['profilePic']?? null;

        if(!$name){
            return $this->json([
                'message' => 'You need to enter a name',
            ], 400);
        }
        if(!$username){
            return $this->json([
                'message' => 'You need to enter a, username',
            ], 400);
        }
        if(!$email){
            return $this->json([
                'message' => 'You need to enter a, email',
            ], 400);
        }
        if(!$password){
            return $this->json([
                'message' => 'You need to enter a password',
            ], 400);
        }
        if($userRepository->findByEmail($email)){
            return $this->json([
                'message' => 'Email already register',
            ], 400);
        }
        $user=new User();
        
        $user->setName($name)
            ->setUsername($username)
            ->setEmail($email)
            ->setRoles(['ROLE_USER'])
            ->setToken(Uuid::uuid4()->toString())
            ->setProfilePic($profilePic)
            ->setPlainPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();
    
    
            return $this->json([
                'message' => 'Team created',
                'user' => [
                    'name' => $user->getName(),
                ]
            ], 201);
        
    }
    #[Route('/login', name: 'login', methods: ["POST"])]
    public function login(
        Request $request, 
        UserRepository $userRepository, 
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $data = json_decode($request->getContent(), true); 
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;


        if (!$email || !$password) {
            return new JsonResponse(['message' => 'Email and password are required'], 400);
        }


        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['message' => 'Invalid credentials'], 401);
        }


        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Invalid credentials'], 401);
        }


        return new JsonResponse([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'key' => $user->getToken()
               
            ]
        ], 200);
    }
}
