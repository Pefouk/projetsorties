<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Lieu;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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

//            ->add('ville', TextType::class,
//                ['label'=>'Ville :'])
//            ->add('rue', TextType::class,
//                ['label'=>'Rue :'])
//            ->add('codePostale', IntegerType::class,
//                ['label'=>'Code postal :'])
//            ->add('latitude', NumberType::class,
//                ['label'=>'Latitude :'])
//            ->add('longitude', NumberType::class,
//                ['label'=>'Longitude :'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
