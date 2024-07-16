<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Enseignant;
use App\Entity\Paiement;
use App\Form\AssignClasseType;
use App\Form\ClasseType;
use App\Form\EleveType;
use App\Form\EnseignantType;
use App\Form\PaiementType;
use App\Form\ReportPaiementType;
use App\Form\UtilisateurType;
use App\Repository\ClasseRepository;
use App\Repository\EleveRepository;
use App\Repository\EnseignantRepository;
use App\Service\HelperService;
use App\Service\PdfService;
use App\Service\SecurityService;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends AbstractController
{
    private $routeError = 'custom_error_page';

    public function __construct(
        private EntityManagerInterface $em,
        private UploaderService $uploaderService,
        private HelperService $helperService,
        private SecurityService $securityService,
    ) {
    }

    #[Route('/eleve', name: 'eleves')]
    public function adminEleve(): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');
        // Je dois avoir la liste des élèves par classe

        // Mais seulement un administrateur peut voir la liste de tous les élèves bien que par classe,
        // Un enseignant quant à lui ne verra que la liste d'élève des classes qui lui sont attribuées.

        $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/eleve/index.html.twig', [
            'eleves' => $eleve,
        ]);
    }

    /**
     * CRUD Elève.
     */
    #[Route('/eleve/{id?0}/edit', name: 'edit_eleve')]
    public function updateEleve(Eleve $eleve = null, Request $req, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            // Redirigez vers la page d'erreur personnalisée
            return $this->redirectToRoute($this->routeError);
        }

        if ($eleve == null) {
            $eleve = new Eleve();
            $new = true;
        }

        // Ici j'ai fait une modification j'ai enlevé EleveType et j'ai mis UtilisateurType
        $form = $this->createForm(UtilisateurType::class, $eleve);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            // si oui,
            // on va ajouter l'objet personne dans la base de données
            $photo = $form->get('photo')->getData();
            $eleve->setPassword(
                $userPasswordHasher->hashPassword(
                    $eleve,
                    $form->get('plainPassword')->getData()
                ));
            if ($photo) {
                $directory = $this->getParameter('photo_directory');
                $eleve->setPhoto($this->uploaderService->uploadPhoto($photo, $directory));
            }
            $this->em->persist($eleve);
            $this->em->flush();
            // Dans votre contrôleur
            $this->addFlash(
                'success', // Le type de message
                "L'élève a été créé avec succès!" // Le message
            );

            return $this->redirectToRoute('eleves', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/eleve/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/eleve/{id}/show', name: 'show_eleve')]
    public function showEleve(Eleve $eleve): Response
    {
        $this->securityService->accessAuthorisation('ROLE_USER');
        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/eleve/show.html.twig', [
            'eleve' => $eleve,
        ]);
    }

    #[Route('/eleve/{id}/delete', name: 'delete_eleve')]
    public function deleteEleve(Eleve $eleve): Response
    {
        $this->securityService->accessAuthorisation('ROLE_SUPER_ADMIN');

        $this->em->remove($eleve);
        $this->em->flush();
        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/eleve/index.html.twig', [
            'eleves' => $eleve,
        ]);
    }

    // Fin CRUD Elève

    #[Route('/enseignant', name: 'enseignants')]
    public function adminEnseignant(): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');

        $enseignant = $this->em->getRepository(Enseignant::class)->findAll();

        return $this->render('admin/enseignant/index.html.twig', [
            'enseignant' => $enseignant,
        ]);
    }

    /**
     * CRUD Enseignant.
     */
    #[Route('/enseignant/{id?0}/edit', name: 'edit_enseignant')]
    public function updateEnseignant(Enseignant $enseignant = null, Request $req): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');

        if ($enseignant == null) {
            $enseignant = new Enseignant();
            $new = true;
        }

        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->helperService->persistEntity($enseignant, $form);

            $this->addFlash(
                'success', // Le type de message
                "L'enseignant a été créé avec succès!" // Le message
            );

            return $this->redirectToRoute('enseignants', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/enseignant/edit.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/enseignant/{id}/show', name: 'show_enseignant')]
    public function showEnseignant(Enseignant $enseignant): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ENSEIGNANT');
        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/enseignant/show.html.twig', [
            'enseignant' => $enseignant,
        ]);
    }

    #[Route('/enseignant/{id}/delete', name: 'delete_enseignant')]
    public function deleteEnseignant(Enseignant $enseignant): Response
    {
        $this->securityService->accessAuthorisation('ROLE_SUPER_ADMIN');
        $this->em->remove($enseignant);
        $this->em->flush();
        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/enseignant/index.html.twig', [
            'enseignant' => $enseignant,
        ]);
    }

    // Fin CRUD Enseignant

    #[Route('/classe', name: 'classe')]
    public function adminClasse(): Response
    {
        $classe = $this->em->getRepository(Classe::class)->findAll();

        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/classe/index.html.twig', [
            'classe' => $classe,
        ]);
    }

    /**
     * CRUD Classe.
     */
    #[Route('/class/{id}/delete', name: 'delete_class')]
    public function deleteClasse(): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');
        $classe = new Classe();
        $this->em->persist($classe);
        $this->em->flush();
        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/enseignant/index.html.twig', [
            'classe' => $classe,
        ]);
    }

    #[Route('/classe/{id?0}/edit', name: 'edit_class')]
    public function editClasse(Classe $classe = null, Request $req): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');
        if ($classe == null) {
            $classe = new Classe();
            $new = true;
        }
        $form = $this->createForm(ClasseType::class, $classe);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($classe);
            $this->em->flush();
            // Dans votre contrôleur
            $this->addFlash(
                'success', // Le type de message
                'La classe a été créé avec succès!' // Le message
            );

            return $this->redirectToRoute('classe', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/classe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/classe/{id}/show', name: 'show_classe')]
    public function showClasse(Classe $classe): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ENSEIGNANT');
        // $eleve = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('admin/classe/show.html.twig', [
            'classe' => $classe,
        ]);
    }

    /**
     * Affiche la fiche d'inscription pour un élève nouvellement inscrit.
     */
    #[Route('/fiche/{id}', name: 'fiche', methods: ['GET'])]
    public function actionTelechargerFicheInscription(PdfService $pdfService, Eleve $eleve): Response
    {
        $this->securityService->accessAuthorisation('ROLE_USER');
        // Vérifie si l'utilisateur a le rôle Admin ou Enseignant
        // Si ce n'est pas le cas, une exception AccessDeniedException est levée
        // if (!$this->securityService->isAdmin() && !$this->securityService->isEnseignant()) {
        //     throw $this->createAccessDeniedException('Seuls les administrateurs et les enseignants peuvent télécharger la fiche d\'inscription.');
        // }

        // Récupère l'élève dont l'ID est passé en paramètre
        // $eleve = $this->eleveRepository->find($id);

        // Génère le PDF de la fiche d'inscription
        return $pdfService->generatePdf('admin/eleve/fiche.html.twig', [
            'eleve' => $eleve,
        ], 'fiche_inscription_'.$eleve->getNom().'-'.$eleve->getPrenom().'.pdf',
        );
    }

    /**
     * Cette méthode permet de payer les frais scolaires.
     */
    #[Route('/payer', name: 'payer')]
    public function payer(Request $request): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');
        $paiement = new Paiement();

        $form = $this->createForm(PaiementType::class, $paiement);

        // Gérez la soumission du formulaire.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($paiement);
            $this->em->flush();

            return $this->redirectToRoute('recu', ['id' => $paiement->getId()]);
        }

        return $this->render('admin/paiement/payer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Cette méthode génère le reçu et reçoit le paramètre id directement de la méthode payer().
     */
    #[Route('/recu/{id}', name: 'recu', methods: ['GET'])]
    public function makeReceipt(PdfService $pdfService, Paiement $paiement = null, EleveRepository $repoEleve): Response
    {
        $this->securityService->accessAuthorisation('ROLE_USER');
        $eleve = $repoEleve->find($paiement->getId());

        dd($eleve);

        return $pdfService->generatePdf('admin/paiement/recu.html.twig', [
            'eleve' => $eleve,
            'classe' => $eleve->getClasse(),
            'paiement' => $paiement,
        ], 'reçu_'.$eleve->getNom().'-'.$eleve->getPrenom().'.pdf',
            'A6',
            'portrait');
    }

    /**
     * Cette méthode renvoit l'historique des paiements d'un élève id.
     */
    #[Route('/historic/{id}', name: 'historic', methods: ['GET'])]
    public function actionAfficherHistoriquePaiements(Eleve $eleve = null): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ENSEIGNANT');
        // // Vérifiez si l'utilisateur a les droits nécessaires.
        // if (!$this->securityService->isAdmin()) {
        //     throw $this->createAccessDeniedException();
        // }

        // Récupérez l'élève à partir de l'ID.
        // $eleve = $this->em->getRepository(Eleve::class)->find($eleveId);

        // Récupérez les paiements de l'élève.
        $paiements = $this->em->getRepository(Paiement::class)->findByEleve($eleve);

        // Affichez l'historique des paiements.
        return $this->render('admin/paiement/historique.html.twig', [
            'paiements' => $paiements,
        ]);
    }

    #[Route('/report/{id?0}', name: 'report', methods: ['GET', 'POST'])]
    public function actionGenererRapportPaiements(Request $request, Classe $classe = null, PdfService $pdfService): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ENSEIGNANT');
        $form = $this->createForm(ReportPaiementType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données du formulaire.
            $data = $form->getData();
            $date = explode('-', $data['date']);
            $mois = $date[0];
            $annee = $date[1];

            // Récupérez tous les paiements pour le mois donné et la classe donnée.
            $paiements = $this->em->getRepository(Paiement::class)->findByMonthAndClasse($mois, $annee, $classe);

            // Générez le rapport.
            return $pdfService->generatePdf('admin/paiement/rapport.html.twig', [
                'paiements' => $paiements,
                'classe' => $classe,
                'mois' => $mois,
                'annee' => $annee,
            ], 'rapport.pdf');
        }

        // Affichez le formulaire.
        return $this->render('admin/paiement/form_rapport.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /*
        #[Route('/try', name: 'try')]
        public function try(ManagerRegistry $em)
        {
            $manager = $em->getManager();
            $classes = $this->em->getRepository(Classe::class)->find(14);
            $prof = $this->em->getRepository(Enseignant::class)->find(104);

            $classes->addEnseignant($prof);
            dd($classes);
            $manager > flush();
            dd('fini');
            $incrementor = 1;
            foreach ($classes as $classroom) {
                $classroom->addEnseignant($prof[$incrementor]);
                ++$incrementor;
                // $tab[] = $prof[$incrementor]; // $classroom->getEnseignants()[0];

                $manager->persist($classroom);
            }// dd($tab);
            $manager > flush();
            dd('Done');
        }
     */
    #[Route('/assign-classe', name: 'assign_classe', methods: ['GET', 'POST'])]
    public function assignClasse(Request $request, EnseignantRepository $enseignantRepository, ClasseRepository $classeRepository, EntityManagerInterface $entityManager): Response
    {
        $this->securityService->accessAuthorisation('ROLE_ADMIN');
        $form = $this->createForm(AssignClasseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $enseignant = $data['enseignant'];
            $classes = $data['classes'];

            foreach ($classes as $classe) {
                $enseignant->addClasse($classe);
            }

            $entityManager->persist($enseignant);
            $entityManager->flush();

            $this->addFlash('success', 'Classes assignées avec succès à l\'enseignant.');

            return $this->redirectToRoute('assign_classe');
        }

        return $this->render('enseignant/assign_classe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
