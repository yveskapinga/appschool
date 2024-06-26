<?php

namespace App\Controller;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Paiement;
use App\Form\PaiementType;
use App\Service\PdfService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Payum\Core\Payum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/paiement', name: 'app_paiement_')]
class PaiementController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SecurityService $securityService)
    {
    }

    #[Route('/payer', name: 'payer')]
    public function payer(Request $request): Response
    {
        $paiement = new Paiement();

        $form = $this->createForm(PaiementType::class, $paiement);

        // Gérez la soumission du formulaire.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($paiement);
            $this->em->flush();

            return $this->redirectToRoute('app_paiement_recu', ['id' => $paiement->getId()]);
        }

        return $this->render('paiement/payer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Cette méthode génère le reçu et reçoit le paramètre id directement de la méthode payer()
     */
    #[Route('/recu/{id}', name: 'recu', methods: ['GET'])]
    public function makeReceipt(PdfService $pdfService, Paiement $paiement): Response
    {
        $eleve = $paiement->getEleve();

        return $pdfService->generatePdf('paiement/recu.html.twig', [
            'eleve' => $eleve,
            'paiement' => $paiement,
        ], 'reçu_'.$eleve->getNom().'-'.$eleve->getPrenom().'.pdf',
            'A6',
            'portrait');
    }

    #[Route('/online', name: 'online', methods: ['POST'])]
    public function actionEnregistrerPaiement(Request $request, Payum $payum): Response
    {
        // Vérifiez si l'utilisateur a les droits nécessaires.
        if (!$this->securityService->isAdmin()) {
            throw $this->createAccessDeniedException();
        }
        // Créez une nouvelle instance de Paiement.
        $paiement = new Paiement();

        // Créez un formulaire pour le paiement.
        $form = $this->createForm(PaiementType::class, $paiement);

        // Gérez la soumission du formulaire.
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Configurez le paiement avec Payum.
            $gatewayName = 'paypal'; // ou 'visa', 'mobile_money', etc.
            $storage = $payum->getStorage('App\Entity\Paiement');
            $payment = $storage->create();
            $payment->setNumber(uniqid());
            $payment->setCurrencyCode('EUR');
            $payment->setTotalAmount($paiement->getMontant() * 100); // convertir en centimes
            $payment->setDescription('Description du paiement');
            $payment->setClientId($paiement->getEleve()->getId());
            $payment->setClientEmail($paiement->getEleve()->getEmail());
            $storage->update($payment);

            // Créez un token de capture.
            $captureToken = $payum->getTokenFactory()->createCaptureToken(
                $gatewayName,
                $payment,
                'paiement_confirm' // la route vers laquelle rediriger après le paiement
            );

            // Redirigez vers la passerelle de paiement.
            return $this->redirect($captureToken->getTargetUrl());
        }

        // Affichez le formulaire de paiement.
        return $this->render('paiement/enregistrer.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/historic/{eleveId}', name: 'historic', methods: ['GET'])]
    public function actionAfficherHistoriquePaiements(Eleve $eleve = null): Response
    {
        // Vérifiez si l'utilisateur a les droits nécessaires.
        if (!$this->securityService->isAdmin()) {
            throw $this->createAccessDeniedException();
        }

        // Récupérez l'élève à partir de l'ID.
        // $eleve = $this->em->getRepository(Eleve::class)->find($eleveId);

        // Récupérez les paiements de l'élève.
        $paiements = $this->em->getRepository(Paiement::class)->findByEleve($eleve);

        // Affichez l'historique des paiements.
        return $this->render('paiement/historique.html.twig', [
            'paiements' => $paiements,
        ]);
    }

    #[Route('/report/{idClasse}', name: 'report', methods: ['POST'])]
    public function actionGenererRapportPaiements(Request $request, Classe $classe, PdfService $pdfService): Response
    {
        // Vérifiez si l'utilisateur a les droits nécessaires.
        // if (!$this->securityService->isAdmin()) {
        //     throw $this->createAccessDeniedException();
        // }

        // Récupérez le mois et l'année à partir de l'objet Request.
        $mois = $request->request->get('mois');
        $annee = $request->request->get('annee');

        // Récupérez tous les paiements pour le mois donné et la classe donnée.
        $paiements = $this->em->getRepository(Paiement::class)->findByMonthAndClasse($mois, $annee, $classe);

        // Générez le rapport.
        return $pdfService->generatePdf('paiement/rapport.html.twig', [
            'paiements' => $paiements,
        ], 'rapport.pdf'
        );
    }
}
