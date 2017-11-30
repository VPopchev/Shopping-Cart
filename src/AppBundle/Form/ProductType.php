<?php

namespace AppBundle\Form;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class)
            ->add('description',TextType::class)
            ->add('quantity',IntegerType::class,[
                    'attr' => [
                        'min' => 1,
                        'max' => 1000
                    ]
                ])
            ->add('status',ChoiceType::class,[
                'choices' => [
                    'Active' => 'Active',
                    'Inactive' => 'Inactive'
                ]
            ])
            ->add('categoryId', EntityType::class,[
                'class' => 'AppBundle\Entity\ProductCategory',
                'choice_label' => function(ProductCategory $category){
                    return $category->getName();
                }
            ])
            ->add('price',MoneyType::class,['currency' => 'BGN'])
            ->add('create',SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }

}
