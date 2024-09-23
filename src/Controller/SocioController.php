<?php

namespace App\Controller;

use App\Entity\Socio;
use App\Repository\SocioRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SocioController extends AbstractController
{
    #[Route('/socios', name: 'socio_index', methods: 'GET')]
    public function index(SocioRepository $socioRepository): JsonResponse
    {
        $socios = $socioRepository->findAll();

        return $this->json($socios);
    }

    #[Route('/socios/{id}', name: 'socio_show', methods: 'GET')]
    public function show(int $id, SocioRepository $socioRepository): JsonResponse
    {
        $socios = $socioRepository->find($id);

        if (!$socios) {
            return $this->json('Nenhum socio encontrado', 404);
        }


        return $this->json($socios);
    }

    #[Route('/socios', name: 'socio_create', methods: ['POST'])]
    public function create(Request $request, SocioRepository $socioRepository): JsonResponse
    {
        $data = $request->request->all();
        $empresaId = $request->get('empresa_id');


        $socio = new Socio();
        $socio->setNome($data['nome']);
        $socio->setCpf($data['cpf']);
        $socio->setStatus($data['status']);
        $socio->setCidade($empresaId);
        $socio->setEmpresaId($data['empresa_id']);
        $socio->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $socio->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $socioRepository->add($socio, true);

        return $this->json([
            'message' => 'Socio criado com sucesso!'
        ], 201);
    }


    #[Route('/socios/{id}', name: 'socio_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, SocioRepository $socioRepository, ManagerRegistry $doctrine): JsonResponse
    {
        $socio = $socioRepository->find($id);
        $data = $request->request->all();
        $entityManager = $doctrine->getManager();



        $socio->setNome($data['nome']);
        $socio->setCpf($data['cpf']);
        $socio->setStatus($data['status']);
        $socio->setCidade($data['cidade']);
        $socio->setEmpresaId($request->get('empresa_id'));
        $socio->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $entityManager->flush();

        return $this->json([
            'message' => 'Socio atualizado com sucesso!'
        ], 201);
    }

    #[Route('/socios/{id}', name: 'socio_update', methods: ['DELETE'])]
    public function delete(int $id, SocioRepository $socioRepository, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $socio = $socioRepository->find($id);

        if (!$socio) {
            return $this->json('Nenhum socio encontrado', 404);
        }

        $entityManager->remove($socio);
        $entityManager->flush();

        return $this->json([
            'message' => 'Socio deletado com sucesso!'
        ], 201);
    }
}
