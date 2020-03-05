<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,
                ['label'=>'Nom de la sortie :',
                    'required'=>false])
            ->add('dateHeureDebut', DateTimeType::class,
                ['label'=>'Date et heure de la sortie :'])
            ->add('duree', TimeType::class,
                ['label'=>'Durée :'])
            ->add('dateLimiteInscription', DateType::class,
                ['label'=>'Date limite d\'inscription :'])
            ->add('nbInscriptionMax', IntegerType::class,
                ['label'=>'Nombre de places :',
                    'required'=>false])
            ->add('infosSortie', TextareaType::class,
                ['label'=>'Description et infos :',
                    'required'=>false])
            ->add('lieu', EntityType::class,
                [   'class'=> Lieu::class,
                    'label'=>'Lieu :',
                    "choice_label"=> 'nom',
                    'placeholder'=> '',
                    'required'=>false,
                    'attr'=>['class'=>'form-control',
                        'onchange'=>'afficherDetails(this)']])
            ->add('enregistrer', SubmitType::class, [
                'label'=>'Enregistrer',
                'attr'=>[
                    'class'=>'btn btn-secondary btn-lg'
                    ]
            ])
            ->add('publier', SubmitType::class, [
                'label'=>'Publier une sortie',
                'attr'=>[
                'value'=>'Publier',
                    'class'=> 'btn btn-secondary btn-lg']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
