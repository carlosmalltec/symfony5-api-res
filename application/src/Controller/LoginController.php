<?php

namespace App\Controller;

use App\Entity\User;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Prefixo para cada rota da controller
 * @Route("/usuario", name="usuario_")
 */
class LoginController extends AbstractController
{
    /**
     * @Route("/index", name="index", methods="GET")
     */
    public function index(): Response
    {
        try {
            if (!empty($usuario = $this->getDoctrine()->getRepository(User::class)->findAll())) {
                return $this->json(
                    $usuario
                );
            }
            throw new \Exception('Não existe dado cadastrado');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/view/{id}", name="view", methods={"GET"})
     */
    public function view(int $id)
    {
        try {
            if (!empty($usuario = $this->getDoctrine()->getRepository(User::class)->find($id))) {
                return $this->json(
                    $usuario
                );
            }
            throw new \Exception('Não foi possível encontrar o usuário');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @Route("/create", name="create", methods={"POST"})
     */
    public function create(Request $req, UserPasswordEncoderInterface $encoder)
    {
        try {
            $data = json_decode($req->getContent());
            $doctrine  = $this->getDoctrine();
            if (empty($data->username))
                throw new \Exception('Login é obrigatório');
            if (empty($data->password))
                throw new \Exception('Senha é obrigatório');

            $user = new User();
            $user->setUsername($data->username);
            $user->setPassword($encoder->encodePassword($user, $data->password));
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

            if (empty($data->username))
                throw new \Exception('Login é obrigatório');
            if (empty($data->password))
                throw new \Exception('Senha é obrigatório');

            $doctrine  = $this->getDoctrine();

            $usuario = $doctrine->getRepository(User::class)->findOneBy([
                'username' => $data->username,
            ]);

            if (!$encoder->isPasswordValid($usuario, $data->password)) {
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
     * @Route("/recuperar", name="recuperar", methods={"POST"})
     */
    public function recuperar(Request $req, UserPasswordEncoderInterface $encoder)
    {
        try {
            $data = json_decode($req->getContent());
            $doctrine  = $this->getDoctrine();
            if (empty($data->username))
                throw new \Exception('Login é obrigatório');
            if (empty($data->password))
                throw new \Exception('Senha é obrigatório');

            $usuario = $doctrine->getRepository(User::class)->findOneBy([
                'username' => $data->username,
            ]);

            if (!$encoder->isPasswordValid($usuario, $data->password)) {
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
