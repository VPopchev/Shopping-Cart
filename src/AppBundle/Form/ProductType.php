<?php

namespace AppBundle\Form;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('categoryId',ChoiceType::class,[
                'choices' => [
                    'Home' => 1,
                    'Office' => 2,
                    'Garden' => 3
                ]
            ])
            ->add('price',NumberType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }

}
