<?php

namespace App\Service;

use Symfony\Component\Security\Core\Security;

class SecurityService
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function isAdmin(): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    public function isEnseignant(): bool
    {
        return $this->security->isGranted('ROLE_ENSEIGNANT');
    }
}
