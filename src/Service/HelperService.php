<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

class HelperService extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private UploaderService $uploaderService)
    {
    }

    public function persistEntity(Utilisateur $entity, FormInterface $form)
    {
        $photoDirectory = $this->getParameter('photo_directory');

        $photo = $form->get('photo')->getData();
        if ($photo && $entity instanceof Utilisateur) {
            $entity->setPhoto($this->uploaderService->uploadPhoto($photo, $photoDirectory));
        }
        $this->em->persist($entity);
        dd('je suis arrivé ici dans HelperService');
        // $this->em->flush();
    }
}
