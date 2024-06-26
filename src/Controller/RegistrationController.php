<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            /*             $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                            (new TemplatedEmail())
                                ->from(new Address('yveskapinga@gmail.com', 'Admin'))
                                ->to($user->getEmail())
                                ->subject('Please Confirm your Email')
                                ->htmlTemplate('registration/confirmation_email.html.twig')
                        ); */

            // do anything else you need here, like send an email

            return $security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /*     #[Route('/verify/email', name: 'app_verify_email')]
        public function verifyUserEmail(Request $request, UtilisateurRepository $utilisateurRepository): Response
        {
            $id = $request->query->get('id');

            if (null === $id) {
                return $this->redirectToRoute('app_register');
            }

            $user = $utilisateurRepository->find($id);

            if (null === $user) {
                return $this->redirectToRoute('app_register');
            }

            // validate email confirmation link, sets User::isVerified=true and persists
            try {
                $this->emailVerifier->handleEmailConfirmation($request, $user);
            } catch (VerifyEmailExceptionInterface $exception) {
                $this->addFlash('verify_email_error', $exception->getReason());

                return $this->redirectToRoute('app_register');
            }

            // @TODO Change the redirect on success and handle or remove the flash message in your templates
            $this->addFlash('success', 'Your email address has been verified.');

            return $this->redirectToRoute('app_register');
        } */
}
