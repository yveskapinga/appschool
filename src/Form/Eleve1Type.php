<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Parents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Eleve1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles')
            ->add('password')
            ->add('prenom')
            ->add('nom')
            ->add('DateDeNaissance', null, [
                'widget' => 'single_text',
            ])
            ->add('sexe')
            ->add('adresse')
            ->add('numeroTelephone')
            ->add('photo')
            ->add('isVerified')
            ->add('createdAt', null, [
                'widget' => 'single_text',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
            ])
            ->add('identificationNationale')
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                // 'choice_label' => 'id',
            ])
            ->add('parents', EntityType::class, [
                'class' => Parents::class,
                // 'choice_label' => 'id',
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
