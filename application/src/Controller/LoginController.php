<?php

namespace App\Controller;

use App\Entity\User;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create(Request $req, UserPasswordEncoderInterface $encoder)
    {
        try {
            $data = json_decode($req->getContent());
            $doctrine  = $this->getDoctrine();
            if (empty($data->login))
                throw new \Exception('Login é obrigatório');
            if (empty($data->senha))
                throw new \Exception('Senha é obrigatório');

            $user = new User();
            $user->setUsername($data->login);
            $user->setPassword($encoder->encodePassword($user, $data->senha));
            $doctrine = $doctrine->getManager();
            $doctrine->persist($user);
            $doctrine->flush();
            return $this->json($user, Response::HTTP_CREATED);
        } catch (\Exception $th) {
            return new Response($th->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $req, UserPasswordEncoderInterface $encoder)
    {
        try {
            $data = json_decode($req->getContent());

            if (empty($data->login))
                throw new \Exception('Login é obrigatório');
            if (empty($data->senha))
                throw new \Exception('Senha é obrigatório');

            $doctrine  = $this->getDoctrine();

            $usuario = $doctrine->getRepository(User::class)->findOneBy([
                'username' => $data->login,
            ]);

            if (!$encoder->isPasswordValid($usuario, $data->senha)) {
                return  $this->json([
                    'erro' => 'Usuário ou senha inválidos'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!empty($usuario)) {
                $token = JWT::encode(['id' => $usuario->getId(), 'username' => $usuario->getUsername()], 'chave', 'HS256');
                return $this->json([
                    'token' => $token
                ]);
            }
            throw new \Exception('Usuário não encontrado');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/recuperar/{id}", name="recuperar", methods={"POST"})
     */
    public function recuperar(Request $req, UserPasswordEncoderInterface $encoder)
    {
        try {
            $data = json_decode($req->getContent());
            $doctrine  = $this->getDoctrine();
            if (empty($data->login))
                throw new \Exception('Login é obrigatório');
            if (empty($data->senha))
                throw new \Exception('Senha é obrigatório');

            $usuario = $doctrine->getRepository(User::class)->findOneBy([
                'username' => $data->login,
            ]);

            if (!$encoder->isPasswordValid($usuario, $data->senha)) {
                return  $this->json([
                    'erro' => 'Usuário ou senha inválidos'
                ], Response::HTTP_UNAUTHORIZED);
            }

            if (!empty($usuario)) {
                return  $this->json([
                    'ok' => 'Enviamos um e-mail com os procedimentos para recuperar a senha'
                ], Response::HTTP_ACCEPTED);
            }

            return new Response('Usuário cadastro com sucesso', Response::HTTP_CREATED);
        } catch (\Exception $th) {
            return new Response($th->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
