<?php

namespace AppBundle\Form;

use AppBundle\Entity\Product;
use AppBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
            ->add('description',TextareaType::class,[
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
            ->add('quantity',IntegerType::class,[
                    'attr' => [
                        'class' => 'form-control',
                        'min' => 1,
                        'max' => 1000
                    ],
                'label_attr' => ['class' => 'col-sm-4 control-label']
                ])
            ->add('isActive',ChoiceType::class,[
                'label' => 'Status',
                'choices' => [
                    'Active' => '1',
                    'Inactive' => '0'
                ],
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
            ->add('category', EntityType::class,[
                'class' => 'AppBundle\Entity\Category',
                'choice_label' => function(Category $category){
                    $parentName = '';
                    if ($category->getParent() != null){
                        $parentName = $category->getParent()->getName() . '/';
                    }
                    return $parentName .$category->getName();
                },
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
            ->add('price',MoneyType::class,[
                'currency' => 'BGN',
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
        ->add('image',FileType::class,[
            'required' => false,
            'label' => 'Product Image',
            'label_attr' => ['class' => 'col-sm-4 control-label'],
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }

}
