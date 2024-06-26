<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Parents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom')
            ->add('nom')
            ->add('email')
            ->add('dateDeNaissance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('sexe')
            ->add('adresse')
            ->add('numeroTelephone')
            ->add('identificationNationale')
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                // 'choice_label' => 'id',
                'required' => false,
            ])
            ->add('parents', EntityType::class, [
                'class' => Parents::class,
                // 'choice_label' => 'id',
                'required' => false,
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
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez envoyer un fichier de format correct',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
