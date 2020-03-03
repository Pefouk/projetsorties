<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=>'Nom : ',
            ])
            ->add('rue', TextType::class, [
                'label'=>'Rue : ',
            ])
            ->add('ville', EntityType::class, [
                'label'=>'Ville : ',
                'choice_label'=>'nom',
                'placeholder'=>' ',
                'class'=> Ville::class,
            ])
            ->add('latitude', NumberType::class, [
                'label'=>'Latitude : ',
            ])
            ->add('longitude', NumberType::class, [
                'label'=>'Longitude : ',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
