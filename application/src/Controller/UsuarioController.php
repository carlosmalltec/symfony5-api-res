<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuario;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prefixo para cada rota da controller
 * @Route("/usuario", name="usuario_")
 */
class UsuarioController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index() : Response
    {
        try {
            if (!empty($usuario = $this->getDoctrine()->getRepository(Usuario::class)->findAll())) {
                return $this->json(
                    $usuario
                );
            }
            throw new \Exception('Não existe dado cadastrado');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), 404);
        }
    }

    /**
     * @Route("/{id}", name="view", methods={"GET"})
     */
    public function view(int $id)
    {
        try {
            if (!empty($usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($id))) {
                return $this->json(
                    $usuario
                );
            }
            throw new \Exception('Não foi possível encontrar o usuário');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), 404);
        }
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     */
    public function create(Request $req)
    {
        // $data = $req->request->all(); $data['nome']
        try {
            $data = json_decode($req->getContent());
            $usuario = new Usuario;

            if (empty($data->nome))
                throw new \Exception('Nome obrigatório');
            if (empty($data->senha))
                throw new \Exception('Senha obrigatório');
            if (empty($data->login))
                throw new \Exception('Login obrigatório');

            $usuario->setNome($data->nome);
            $usuario->setLogin($data->login);
            $usuario->setSenha(md5($data->senha));
            $usuario->setHash(md5(time()));
            $usuario->setCreatedAt(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));

            //Doctrine
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($usuario); // objeto do usuário
            $doctrine->flush(); //salva os dados no banco
            return $this->json($usuario,Response::HTTP_OK);
        } catch (\Exception $th) {
            return new Response($th->getMessage(), 404);
        }
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT","PATCH"})
     */
    public function update(int $id, Request $req)
    {
        // $data = $req->request->all(); $data['nome']
        try {
            $data = json_decode($req->getContent());
            $doctrine  = $this->getDoctrine();
            $usuario = $doctrine->getRepository(Usuario::class)->find($id);
            if (!empty($usuario)) {

                if (!empty($data->nome))
                    $usuario->setNome($data->nome);
                if (!empty($data->senha))
                    $usuario->setSenha(md5($data->senha));
                if (!empty($data->login))
                    $usuario->setLogin($data->login);

                $usuario->setUpdatedAt(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));

                //Doctrine
                $manager = $doctrine->getManager();
                $manager->flush(); //salva os dados no banco
                return $this->json($usuario,Response::HTTP_OK);
            }
            throw new \Exception('Não foi possível encontrar o usuário');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), 404);
        }
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id)
    {
        try {
            $doctrine  = $this->getDoctrine();
            $usuario = $this->getDoctrine()->getRepository(Usuario::class)->find($id);
            if (!empty($usuario)) {
                $manager = $doctrine->getManager();
                $manager->remove($usuario);
                $manager->flush();

                return new Response('Usuário atualizado com sucesso!', Response::HTTP_OK);
            }
            throw new \Exception('Não foi possível encontrar o usuário');
        } catch (\Exception $th) {
            return new Response($th->getMessage(), 404);
        }
    }
}
