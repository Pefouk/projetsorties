<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Campus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label'=>'Pseudo :'
            ])
            ->add('prenom', TextType::class, [
                'label'=>'Prénom :'
            ])
            ->add('nom', TextType::class, [
                'label'=>'Nom :'
            ])
            ->add('telephone', TelType::class, [
                'label'=>'Téléphone :'
            ])
            ->add('mail', EmailType::class, [
                'label'=>'Email :'
            ])
            ->add('motPasse', RepeatedType::class, [
                'type'=>PasswordType::class,
                'invalid_message'=>'Les mots de passe doivent être similaires.',
                'required'=>true,
                'options'=>['attr'=>['class'=>'password-field']],
                'first_options'=>array('label'=>'Mot de passe :'),
                'second_options'=>array('label'=>'Confirmation :')])
                        ->add('campus', null,
                            [
                                'label'=>'Campus',
                                'choice_label'=>'nom'
                            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
