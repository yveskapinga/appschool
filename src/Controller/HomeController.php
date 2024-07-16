<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('template.html.twig');
    }

    #[Route('/manschool', name: 'app_manschool')]
    public function app(): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            // Utilisateur authentifié : redirige vers la route index
            return $this->render('base.html.twig');
        } else {
            // Utilisateur non authentifié : redirige vers la route app_login
            return $this->redirectToRoute('app_login');
        }
    }

    // J'ai ajouté cette méthode qui dirige vers la page d'erreur 403
    #[Route('/erreur-404', name: 'custom_error_page')]
    public function error404(): Response
    {
        return $this->render('pages/error_page.html.twig');
    }

    #[Route('/under_building', name: 'under_building')]
    public function construction(): Response
    {
        return $this->render('pages/under_construction.html.twig');
    }
}
