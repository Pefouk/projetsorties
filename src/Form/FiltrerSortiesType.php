<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrerSortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Nom',
                'required'=> true
            ])
            ->add('recherche', TextType::class, [
                'label' => 'Le nom de la sortie contient',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Recherche...'
                ]
            ])->add('datemin', DateType::class, [
                'label' => 'Entre',
                'required' => false
            ])->add('datemax', DateType::class, [
                'label' => 'Et',
                'required' => false
            ])->add('organise', CheckboxType::class, [
                'required'=>false,
                'label' => 'Sorties dont je suis l\'organisateur/trice'
            ])->add('inscrit', CheckboxType::class, [
                'required'=>false,
                'label' => 'Sorties dont je suis inscrit/e'
            ])->add('nonInscrit', CheckboxType::class, [
                'required'=>false,
                'label' => 'Sorties dont je ne suis pas inscrit/e'
            ])->add('passee', CheckboxType::class, [
                'required'=>false,
                'label' => 'Sorties passées'
            ])->add('Rechercher', SubmitType::class, [
                'validate' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}
