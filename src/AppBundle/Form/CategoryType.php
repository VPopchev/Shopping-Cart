<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class,[
            'attr' => ['class' => 'form-control'],
            'label_attr' => ['class' => 'col-sm-4 control-label']
        ])
        ->add('parent',EntityType::class,[
            'class' => 'AppBundle\Entity\Category',
            'choice_label' => function(Category $category){
                $parentName = '';
                if ($category->getParent() != null){
                    $parentName = $category->getParent()->getName() . '/';
                }
                return $parentName .$category->getName();
            },
            'placeholder' => 'Choice category',
            'required' => false,
            'attr' => ['class' => 'form-control'],
            'label_attr' => ['class' => 'col-sm-4 control-label']
        ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Category::class]);
    }

}
