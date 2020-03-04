<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo :',
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone :',
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('mail', EmailType::class, [
                'label' => 'Email :',
                'attr' => array(
                    'readonly' => true,
                )
            ])
            ->add('campus', null,
                [
                    'disabled' => true,
                    'label' => 'Campus',
                    'choice_label' => 'nom',
                    'attr' => array(
                        'readonly' => true,
                    )
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
