<?php

namespace App\Controller;

use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UsersController extends AbstractController
{
    public function __construct(private SecurityService $securityService)
    {
    }

    #[Route(path: '/profile', name: 'app_users_profil')]
    public function userProfile(): Response
    {
        $user = $this->getUser();
        // dd($user->getPhoto());
        $this->securityService->accessAuthorisation('ROLE_USER');

        return $this->render('pages/users_profile.html.twig');
    }
}
