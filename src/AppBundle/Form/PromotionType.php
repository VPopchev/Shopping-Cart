<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',TextType::class,[
            'attr' => ['class' => 'form-control'],
            'label_attr' => ['class' => 'col-sm-4 control-label']
        ])
            ->add('startDate',DateType::class,[
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endDate',DateType::class,[
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Product' => 1,
                    'User' => 2,
                ],
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
            ->add('discount',NumberType::class,[
                'attr' => ['class' => 'form-control'],
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Promotion'
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_promotion_type';
    }
}
