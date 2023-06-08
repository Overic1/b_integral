<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class BanquecaisseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50'
                ],
                'label' => 'Label',
                'label_attr' => [
                    'class' => 'form-label '
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])

            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Banque' => 1,
                    'Caisse' => 2,
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Type',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('clos', ChoiceType::class, [
                'choices'  => [
                    'Ouvert' => '1',
                    'Fermé' => '0',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Status',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            
            ->add('country_id', ChoiceType::class, [
                'choices' => $options['choices'],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Pays',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])
            ->add('currency_code', ChoiceType::class, [
                'choices'  => [
                    'CFA' => 'XOF',
                ],  
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Device',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ],
                'label' => 'Enregistrer un client'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [],
            // Configure your form options here
        ]);
    }
}