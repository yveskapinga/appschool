<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Repository\EleveRepository;
use App\Repository\UtilisateurRepository;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/eleve', name: 'app_eleve_')]
class Eleve1Controller extends AbstractController
{
    public function __construct(
        private SecurityService $securityService,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/index', name: 'index1')]
    public function index(): Response
    {
        $eleves = $this->em->getRepository(Eleve::class)->findAll();

        return $this->render('eleve/index.html.twig', [
            'eleves' => $eleves,
        ]);
    }

    #[Route('/rechercher', name: 'rechercher_eleve', methods: ['GET', 'POST'])]
    public function rechercherEleve(Request $request, EleveRepository $eleveRepository): Response
    {
        dd($this->securityService->isAdmin());
        if (!$this->securityService->isAdmin() && !$this->securityService->isEnseignant()) {
            dd('Tu es enseignant ou administrateur');
            throw $this->createAccessDeniedException('Seuls les administrateurs et les enseignants peuvent rechercher un élève.');
        }
        dd('on est élève');
        $critere = $request->request->get('critere');
        $eleves = $eleveRepository->rechercherEleve($critere);

        return $this->render('eleve/rechercher.html.twig', [
            'eleves' => $eleves,
        ]);
    }

    #[Route('/update', name: 'update')]
    public function updateUser(UtilisateurRepository $repos, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        // $user = $this->getUser();
        $user = $repos->find(1);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user, 'Jmas1997,'
            )
        );
        $this->em->persist($user);
        // dd($user);
        $this->em->flush();
        dd('avec succès');

        return new Response();
    }
}
