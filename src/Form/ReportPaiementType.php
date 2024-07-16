<?php

// src/Form/AssignClasseType.php

namespace App\Form;

use App\Entity\Classe;
use App\Entity\Paiement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportPaiementType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $dates = $this->em->getRepository(Paiement::class)->findDistinctMonthsAndYears();
        $choices = [];
        foreach ($dates as $date) {
            $choices[sprintf('%02d-%d', $date['month'], $date['year'])] = $date;
        }

        $builder
            ->add('date', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Date (Mois-Année)',
            ])
            ->add('classe', EntityType::class, [
                'class' => Classe::class,
                'choice_label' => '__toString', // Utilise la fonction __toString() de l'entité Classe
                'label' => 'Classe',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
