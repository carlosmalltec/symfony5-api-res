<?php

namespace App\Controller;

use App\Entity\Produto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prefixo para cada rota da controller
 * @Route("/produto", name="produto_")
 */
class ProdutoController extends AbstractController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index() : Response
    {
        try {
            if (!empty($produto = $this->getDoctrine()->getRepository(Produto::class)->findAll())) {
                return $this->json($produto);
            }
            throw new \Exception('Nenhum dado entrado');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage(),'produto'=>[]]);
        }
    }

    /**
     * @Route("/{id}", name="view", methods={"GET"})
     */
    public function view(int $id)
    {
        try {
            if (!empty($produto = $this->getDoctrine()->getRepository(Produto::class)->find($id))) {
                return  $this->json(['msg' => 'Produto encontrado!','produto'=>$produto]);
            }
            throw new \Exception('Nenhum dado entrado');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage(),'produto'=>[]]);
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
            $produto = new Produto;

            if (empty($data->titulo))
                throw new \Exception('Título obrigatório');
            if (empty($data->descricao))
                throw new \Exception('Descrição obrigatório');
            if (empty($data->preco))
                throw new \Exception('Preço obrigatório');

            $produto->setTitulo($data->titulo);
            $produto->setDescricao($data->descricao);
            $produto->setPreco($data->preco);
            $produto->setStatus(1);
            $produto->setCreateAt(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));

            //Doctrine
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($produto); // objeto do usuário
            $doctrine->flush(); //salva os dados no banco
            return  $this->json(['msg' => 'Produto cadastrado!','produto'=>$produto]);
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage(),'produto'=>[]]);
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
            $produto = $doctrine->getRepository(Produto::class)->find($id);
            if (!empty($produto)) {

                if (!empty($data->titulo))
                    $produto->setTitulo($data->titulo);
                if (!empty($data->descricao))
                    $produto->setDescricao($data->descricao);
                if (!empty($data->preco))
                    $produto->setPreco($data->preco);

                $produto->setUpdateAt(new \DateTime('now', new \DateTimeZone('America/Sao_Paulo')));

                //Doctrine
                $manager = $doctrine->getManager();
                $manager->flush(); //salva os dados no banco
                return  $this->json(['msg' => 'Produto atualizado!','produto'=>$produto]);
            }
            throw new \Exception('Não foi possível encontrar o produto');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage(),'produto'=>[]]);
        }
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id)
    {
        try {
            $doctrine  = $this->getDoctrine();
            $produto = $this->getDoctrine()->getRepository(Produto::class)->find($id);
            if (!empty($produto)) {
                $manager = $doctrine->getManager();
                $manager->remove($produto);
                $manager->flush();
                return  $this->json(['msg' => "Produto {$produto->getTitulo()} excluído com sucesso",'produto'=>[]]);
            }
            throw new \Exception('Não foi possível encontrar o produto');
        } catch (\Exception $th) {
            return  $this->json(['msg' => $th->getMessage(),'produto'=>[]]);
        }
    }
}
