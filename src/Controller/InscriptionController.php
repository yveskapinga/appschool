<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\EleveType;
use App\Repository\EleveRepository;
use App\Service\PdfService;
use App\Service\SecurityService;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/inscription', name: 'app_inscription_')]
class InscriptionController extends AbstractController
{
    public function __construct(
        private SecurityService $securityService,
        private EntityManagerInterface $em,
        private EleveRepository $eleveRepository
    ) {
    }

    #[Route('/index', name: 'list')]
    public function index(): Response
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

    #[Route('/inscrire/{id?}', name: 'inscrire', methods: ['GET', 'POST'])]
    public function actionInscription(Request $request, Eleve $eleve = null, UploaderService $uploaderService): Response
    {
        // Vérifie si l'utilisateur a le rôle Admin
        // Si ce n'est pas le cas, une exception AccessDeniedException est levée
        /*         if (!$this->securityService->isAdmin()) {
                    throw $this->createAccessDeniedException('Seuls les administrateurs peuvent inscrire un élève.');
                } */

        // Récupère l'élève dont l'ID est passé en paramètre, ou crée un nouvel élève si aucun ID n'est fourni
        // $eleve = $id ? $this->eleveRepository->find($id) : new Eleve();

        // Crée le formulaire d'inscription et le lie à l'élève
        $new = false;
        if (!$eleve) {
            $new = true;
            $eleve = new Eleve();
        }
        $form = $this->createForm(EleveType::class, $eleve);
        if (isset($new) and $new) {
            $form->remove('parents');
        }
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persiste l'élève dans la base de données
            $photo = $form->get('photo')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('photo_directory');
                $eleve->setPhoto($uploaderService->uploadPhoto($photo, $directory));
            }
            $this->em->persist($eleve);
            $this->em->flush();

            $this->addFlash('success', 'L\'élève a été inscrit avec succès.');

            // Redirige vers la liste des inscriptions

            return $this->redirectToRoute('app_inscription_fiche', ['id' => $eleve->getId()]);
        }

        // Affiche le formulaire d'inscription
        return $this->render('inscription/form.html.twig', [
            'eleve' => $eleve,
            'new' => $new,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/liste-inscriptions', name: 'liste_inscriptions', methods: ['GET'])]
    public function actionAfficherListeInscriptions(): Response
    {
        // Vérifie si l'utilisateur a le rôle Admin ou Enseignant
        // Si ce n'est pas le cas, une exception AccessDeniedException est levée
        if (!$this->securityService->isAdmin() && !$this->securityService->isEnseignant()) {
            throw $this->createAccessDeniedException('Seuls les administrateurs et les enseignants peuvent voir la liste des inscriptions.');
        }

        // Récupère tous les élèves triés par la date de leur dernière mise à jour
        $eleves = $this->eleveRepository->trouverInscriptionsEtReinscriptions();

        // Affiche la liste des inscriptions
        return $this->render('inscription/liste.html.twig', [
            'eleves' => $eleves,
        ]);
    }

    #[Route('/fiche/{id}', name: 'fiche', methods: ['GET'])]
    public function actionTelechargerFicheInscription(PdfService $pdfService, Eleve $eleve): Response
    {
        // Vérifie si l'utilisateur a le rôle Admin ou Enseignant
        // Si ce n'est pas le cas, une exception AccessDeniedException est levée
        // if (!$this->securityService->isAdmin() && !$this->securityService->isEnseignant()) {
        //     throw $this->createAccessDeniedException('Seuls les administrateurs et les enseignants peuvent télécharger la fiche d\'inscription.');
        // }

        // Récupère l'élève dont l'ID est passé en paramètre
        // $eleve = $this->eleveRepository->find($id);

        // Génère le PDF de la fiche d'inscription
        return $pdfService->generatePdf('inscription/fiche.html.twig', [
            'eleve' => $eleve,
        ], 'fiche_inscription_'.$eleve->getNom().'-'.$eleve->getPrenom().'.pdf',
        );
    }
}
