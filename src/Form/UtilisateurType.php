<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Personnel;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('prenom', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                new Length(['max' => 255, 'maxMessage' => 'Le prénom ne peut pas dépasser 255 caractères.']),
            ],
        ])
        ->add('nom', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le nom ne peut pas être vide.']),
                new Length(['max' => 255, 'maxMessage' => 'Le nom ne peut pas dépasser 255 caractères.']),
            ],
        ])
        ->add('postnom', TextType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Le postnom ne peut pas être vide.']),
                new Length(['max' => 255, 'maxMessage' => 'Le nom ne peut pas dépasser 255 caractères.']),
            ],
        ])
        ->add('DateDeNaissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Naissance',
            ])
        ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Masculin' => 'M',
                    'Féminin' => 'F',
                ], ])
        ->add('adresse')
        ->add('numeroTelephone', TelType::class, [
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'placeholder' => '0XX XX XXX XX',
                    'pattern' => '0[0-9]{9}', // Expression régulière pour le format attendu
                ],
                'constraints' => [
                    new Regex([
                        'pattern' => '/^0[0-9]{9}$/',
                        'message' => 'Le numéro de téléphone doit être au format 0XXXXXXXXX.',
                    ]),
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Votre image de profil (Des fichiers images uniquement)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ]),
                ],
            ])

            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Enseignant' => 'ROLE_ENSEIGNANT',
                    'Élève' => 'ROLE_USER',
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    // Ajoutez d'autres rôles ici si nécessaire
                ],
                'multiple' => true, // Si le rôle est un tableau
                'expanded' => true, // Afficher sous forme de boutons radio
            ])

            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => "L'adresse e-mail ne peut pas être vide."]),
                    new Email(['message' => "L'adresse e-mail n'est pas valide."]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                        'message' => "L'adresse e-mail n'est pas au bon format.",
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuilez saisir votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de page doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);

        $entity = $builder->getData();

        if ($entity instanceof Personnel) {
            $builder->add('matricule')
            ->add('fonction', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'enseignant' => 'Enseignant',
                    'administration' => 'Administration',
                ], ]);
        } elseif ($entity instanceof Eleve) {
            $builder->add('identificationNationale');
        }

        $builder->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'Vous devez accepter les termes d\'_utilisation.',
                ]),
            ],
        ]);

        /*
         * fin de la partie
         */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
