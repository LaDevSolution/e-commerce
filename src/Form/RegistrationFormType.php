<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ requis.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom semble incohérent',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ requis.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre prénom semble incohérent',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ requis.',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre adresse mail doit contenir {{ limit }} caractères minimum',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les conditions d\'utilisation de vos données personnelles',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'J\'accepte les conditions d\'utilisation des données personnelles.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password', 
                    'class' => 'inputPassword'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Champ requis.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir {{ limit }} caractères minimum',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
