<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\Eleve1Type;
use App\Repository\EleveRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestionEnseignant', name: 'app_gestion_enseignant_')]
class EleveController extends AbstractController
{
    #[Route('/enseignant/{id}/assigner-matiere', name: 'assigner_matiere')]
    public function assignerMatiere(
        Request $request, 
        Enseignant $enseignant=null, 
        MatiereRepository $matiereRepository,
        EntityManagerInterface $em,
        ): Response
    {
        $form = $this->createFormBuilder()
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choices' => $matiereRepository->findAll(),
                'choice_label' => 'designation',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matiere = $form->get('matiere')->getData();

            if (!$enseignant->getMatieres()->contains($matiere)) {
                $enseignant->addMatiere($matiere);
                $this->em->flush();

                $this->addFlash('success', 'La matière a été assignée avec succès à l\'enseignant.');
            } else {
                $this->addFlash('warning', 'L\'enseignant a déjà cette matière assignée.');
            }

            return $this->redirectToRoute('enseignant_show', ['id' => $enseignant->getId()]);
        }

        return $this->render('gestion_enseignant/assigner_matiere.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/enseignant/{id}/modifier-matiere/{matiereId}', name: 'modifier_matiere')]
    public function modifierMatiere(
        Request $request, 
        Enseignant $enseignant=null, 
        Matiere $matiere=null, // c'est à rétirer pour ressembler à la méthode assigner matière
        EntityManagerInterface $em,
    ): Response
    {
        $form = $this->createFormBuilder()
            ->add('matiere', EntityType::class, [
                'class' => Matiere::class,
                'choices' => $matiereRepository->findAll(),
                'choice_label' => 'designation',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $matiere = $form->get('matiere')->getData();

            if (!$enseignant->getMatieres()->contains($matiere)) {
                $enseignant->addMatiere($matiere);
                $message = 'ajouté'
            } else {
                $enseignant->removeMatiere($matiere);
                $message = 'retiré'
            }

            $this->addFlash('success', 'La matière a été '.$message.' avec succès de l\'enseignant '.$enseignant->getNom);

            $this->em->flush();
            return $this->redirectToRoute('enseignant_show', ['id' => $enseignant->getId()]);
        }

        return $this->render('gestion_enseignant/assigner_matiere.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }


    // La méthode adjoindreClasse 

    #[Route('/enseignant/{id}/adjoindre-classe', name: 'enseignant_adjoindre_classe')]
    public function adjoindreClasse(Request $request, Enseignant $enseignant, ClasseRepository $classeRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choices' => $classeRepository->findAll(),
                'choice_label' => 'designation',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe = $form->get('classe')->getData();

            if (!$enseignant->getClasses()->contains($classe)) {
                $enseignant->addClass($classe);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'La classe a été adjointe avec succès à l\'enseignant.');
            } else {
                $this->addFlash('warning', 'L\'enseignant a déjà cette classe adjointe.');
            }

            return $this->redirectToRoute('enseignant_show', ['id' => $enseignant->getId()]);
        }

        return $this->render('enseignant/adjoindre_classe.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }




    // La méthode retirerClasse

    #[Route('/enseignant/{id}/retirer-classe', name: 'enseignant_retirer_classe')]
    public function retirerClasse(Request $request, Enseignant $enseignant, ClasseRepository $classeRepository): Response
    {
        $form = $this->createFormBuilder()
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choices' => $classeRepository->findAll(),
                'choice_label' => 'designation',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classe = $form->get('classe')->getData();

            if ($enseignant->getClasses()->contains($classe)) {
                $enseignant->removeClass($classe);
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', 'La classe a été retirée avec succès de l\'enseignant.');
            } else {
                $this->addFlash('warning', 'L\'enseignant n\'a pas cette classe adjointe.');
            }

            return $this->redirectToRoute('enseignant_show', ['id' => $enseignant->getId()]);
        }

        return $this->render('enseignant/retirer_classe.html.twig', [
            'enseignant' => $enseignant,
            'form' => $form->createView(),
        ]);
    }


    //La méthode getHistoriqueClasses

    #[Route('/enseignant/{id}/historique-classe', name: 'enseignant_historique_classe')]
    public function getHistoriqueClasse(Enseignant $enseignant): Response
    {
        $classes = $enseignant->getClasses();

        return $this->render('enseignant/historique_classe.html.twig', [
            'enseignant' => $enseignant,
            'classes' => $classes,
        ]);
    }


    // La méthode getHistoriqueMatieres

    #[Route('/enseignant/{id}/historique-matiere', name: 'enseignant_historique_matiere')]
    public function getHistoriqueMatiere(Enseignant $enseignant): Response
    {
        $matieres = $enseignant->getMatieres();

        return $this->render('enseignant/historique_matiere.html.twig', [
            'enseignant' => $enseignant,
            'matieres' => $matieres,
        ]);
    }


    //La méthode getEnseignantsParClasse

    #[Route('/classe/{id}/enseignants', name: 'classe_enseignants')]
    public function getEnseignantsParClasse(Classe $classe): Response
    {
        $enseignants = $classe->getEnseignants();

        return $this->render('classe/enseignant_par_classe.html.twig', [
            'classe' => $classe,
            'enseignants' => $enseignants,
        ]);
    }


}