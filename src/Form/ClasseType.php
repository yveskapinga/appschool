<?php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Enseignant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('classe')
            ->add('designation')
            ->add('section', ChoiceType::class, [
                // 'mapped' => false,
                'choices' => [
                    'Maternelle' => 'maternelle',
                    'Primaire' => 'primaire',
                    'Humanité' => 'humanite',
                ],
            ])
            ->add('enseignants', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('professeurPrincipal', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => 'nom',
            ])
            ->add('Enregistrer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
