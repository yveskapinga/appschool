<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\Eleve1Type;
use App\Form\EleveType;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/eleve', name: 'app_eleve_')]
class EleveController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(EleveRepository $eleveRepository): Response
    {
        return $this->render('eleve/index.html.twig', [
            'eleves' => $eleveRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eleve = new Eleve();
<<<<<<< HEAD
        $form = $this->createForm(EleveType::class, $eleve);
=======
        $parents = new Parents();
        $eleve->setParents($parents);

        $form = $this->createForm(Eleve1Type::class, $eleve);

                // Retirer les champs ici
        $form->get('parents')->remove('password');
        $form->get('parents')->remove('roles');
        $form->get('parents')->remove('dateDeNaissance');
        $form->get('parents')->remove('photo');
        $form->get('parents')->remove('isVerified');

>>>>>>> ab957363ad7b49516ce7ae8d38ce1e848b12dc33
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($eleve);
            // $entityManager->persist($eleve);
            // $entityManager->flush();

            return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eleve/new.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Eleve $eleve): Response
    {
        return $this->render('eleve/show.html.twig', [
            'eleve' => $eleve,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Eleve1Type::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eleve->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($eleve);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
    }
}
