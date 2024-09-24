<?php

namespace App\Controller;

use App\Entity\Empresa;
use App\Repository\EmpresaRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmpresaController extends AbstractController
{

    #[Route('/empresas', name: 'empresa_index', methods: 'GET')]
    public function index(EmpresaRepository $empresaRepository): JsonResponse
    {
        $empresas = $empresaRepository->findAll();

        return $this->json($empresas);
    }

    #[Route('/empresas/{id}', name: 'empresa_show', methods: ['GET'])]
    public function show(int $id, EmpresaRepository $empresaRepository): JsonResponse
    {
        $empresa = $empresaRepository->find($id);

        return $this->json($empresa);
    }

    #[Route('/empresas', name: 'empresa_create', methods: ['POST'])]
    public function create(Request $request, EmpresaRepository $empresaRepository): JsonResponse
    {
        $data = $request->request->all();

        $empresa = new Empresa();
        $empresa->setNome($data['nome']);
        $empresa->setCnpj($data['cnpj']);
        $empresa->setStatus($data['status']);
        $empresa->setCidade($data['cidade']);
        $empresa->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $empresa->setUdpatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $empresaRepository->add($empresa, true);

        return $this->json([
            'message' => 'Empresa criada com sucesso!',
            'data' => $empresa,
        ], 201);
    }

    #[Route('/empresas/{id}', name: 'empresa_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, EmpresaRepository $empresaRepository, ManagerRegistry $doctrine): JsonResponse
    {
        $empresa = $empresaRepository->find($id);
        $data = $request->request->all();
        $entityManager = $doctrine->getManager();



        $empresa->setNome($data['nome']);
        $empresa->setCnpj($data['cnpj']);
        $empresa->setStatus($data['status']);
        $empresa->setCidade($data['cidade']);
        $empresa->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $empresa->setUdpatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $entityManager->flush();

        return $this->json([
            'message' => 'Empresa atualizada com sucesso!'
        ], 201);
    }

    #[Route('/empresas/{id}', name: 'empresa_delete', methods: ['DELETE'])]
    public function delete(int $id, EmpresaRepository $empresaRepository, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $empresa = $empresaRepository->find($id);

        if (!$empresa) {
            return $this->json('Nenhuma Empresa encontrada', 404);
        }

        $entityManager->remove($empresa);
        $entityManager->flush();

        return $this->json([
            'message' => 'Empresa deletada com sucesso!'
        ], 201);
    }
}
