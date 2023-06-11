<?php

namespace App\Form;

use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{
    // private $defaultImagePath;

    // public function __construct(string $defaultImagePath)
    // {
    //     $this->defaultImagePath = $defaultImagePath;
    // }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('label', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    'maxlength' => '50'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'form-label '
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])

            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    // 'maxlength' => '50'
                ],
                'label' => 'description',
                'label_attr' => [
                    'class' => 'form-label'
                ],
            ])

            ->add('barcode', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                    // 'maxlength' => '50'
                ],
                'label' => 'Barcode',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])

            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                ],
                'label' => 'Price',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('price_ttc', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                ],
                'label' => 'Price_ttc',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('status_buy', ChoiceType::class, [
                'choices'  => [
                'hors achat' => 0,
                'en achat' => 1,
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Status_buy',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'hors vente' => 0,
                    'en vente' => 1,
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

            ->add('tva_tx', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                ],
                'label' => 'Tva_tx',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('options_taxgroup', ChoiceType::class, [
                'choices' => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                    'D' => 'D',
                    'E' => 'E',
                    'F' => 'F',
                    'AIB-A' => 'AIB-A',
                    'AIB-B' => 'AIB-B'
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'options_taxgroup',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('options_taxspecific', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                ],
                'label' => 'tva spécifique',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])

            ->add('options_qty', NumberType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'minlength' => '2',
                ],
                'label' => 'Stock réel',
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new Assert\NotBlank()
                ]
            ])


            ->add('image', FileType::class, [
                // 'data' => 'images/default.jpeg',
                // 'data_class' => null, 
                'required'   => false,
                // 'empty_data' => 'images/default.jpeg',
                'label' => 'Image',
                'attr' => [
                    'class' => 'form-control'
                ], 
                'label_attr' => [   
                'class' => 'form-label'
            ],
            
                // 'data' => new File($options['default_image_path']),
                'constraints' => [
                    // new Assert\NotBlank(),
                    new Assert\File([
                        'maxSize' => '5M',
                    ]),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary w-100'
                ],
                'label' => 'Enregistrer un produit'
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => Produit::class,
            // 'default_image_path' => $this->defaultImagePath,
            // Configure your form options here
        ]);
    }
}