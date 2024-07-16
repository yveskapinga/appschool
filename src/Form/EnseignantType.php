<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Enseignant;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnseignantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('prenom')
            ->add('nom')
            ->add('DateDeNaissance', null, [
                'widget' => 'single_text',
            ])
            ->add('sexe', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Masculin' => 'M',
                    'Féminin' => 'F',
                ],])
            ->add('adresse')
            ->add('numeroTelephone')
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
                    ])
                ],
            ])
            ->add('fonction')
            ->add('matricule')
            ->add('matieres', EntityType::class, [
                'class' => Matiere::class,
                'multiple' => true,
                'required' => false,
                'choice_label' => 'designation',
            ])
            // ->add('classes', EntityType::class, [
            //     'class' => Classe::class,
            //     'multiple' => true,
            // ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
        ]);
    }
}
