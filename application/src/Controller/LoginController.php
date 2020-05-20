<?php

namespace App\Controller;

use App\Components\HelperEmail;
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
    public function index()
    {
        try {
            if (!empty($usuario = $this->getDoctrine()->getRepository(User::class)->findAll())) {
                return  $this->json($usuario);
            }
            throw new \Exception('Não existe dado cadastrado');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage()]);
        }
    }

    /**
     * @Route("/view/{id}", name="view", methods={"GET"})
     */
    public function view(int $id)
    {
        try {
            if (!empty($usuario = $this->getDoctrine()->getRepository(User::class)->find($id))) {
                return  $this->json(['msg' => 'Usuário encontrado', 'usuario' => $usuario]);
            }
            throw new \Exception('Não foi possível encontrar o usuário');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage()]);
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
            return  $this->json(['msg' => 'Cadastro realizado com sucesso!', 'usuario' => $user]);
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage()]);
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
                throw new \Exception('Usuário ou senha inválidos');
            }

            if (!empty($usuario)) {
                $token = JWT::encode(['id' => $usuario->getId(), 'username' => $usuario->getUsername()], $_ENV['APP_SECRET'], 'HS256');
                return  $this->json(['msg' => 'Token gerado com sucesso', 'token' => $token,'usuario'=>$usuario]);
            }
            throw new \Exception('Usuário não encontrado');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage()]);
        }
    }

    /**
     * @Route("/recuperar", name="recuperar", methods={"POST"})
     */
    public function recuperar(Request $req)
    {
        try {
            $data = json_decode($req->getContent());
            $doctrine  = $this->getDoctrine();
            if (empty($data->username))
                throw new \Exception('Login é obrigatório');

            $usuario = $doctrine->getRepository(User::class)->findOneBy([
                'username' => $data->username,
            ]);

            if (!empty($usuario)) {
                $msg = 'Não foi possível enviar o e-mail com os procedimento para recuperar sua senha';
                if ($this->enviarEmail()) {
                    $msg = 'Enviamos um e-mail com os procedimentos para recuperar senha!';
                }
                return  $this->json(['msg' => $msg, 'usuario' => $usuario->getUsername()]);
            }
            throw new \Exception('Nenhum usuário encontrado!');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage()]);
        }
    }

    public function enviarEmail()
    {
        $sendMail = new HelperEmail();
        $sendMail->sendEmail(
            ['carlosmalltec@gmail.com' => 'Carlos Santos'], //remetente
            ['contatomalltec@gmail.com' => 'Carlos Santos'], // destinatario
            'Recuperar senha', //assunto
            'Clique no link para recuperar sua senha'
        ); //corpo da mensagem (TEXT)
        return $sendMail->getResult();
    }
}
