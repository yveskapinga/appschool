<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Enseignant;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
            ->add('sexe')
            ->add('adresse')
            ->add('numeroTelephone')
            ->add('photo')
            ->add('fonction')
            ->add('matricule')
            ->add('matieres', MatiereType::class, [
               // 'class' => Matiere::class,
                'multiple' => true,
                'required' => false,
            ])
            // ->add('classes', EntityType::class, [
            //     'class' => Classe::class,
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Enseignant::class,
        ]);
    }
}
