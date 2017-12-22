<?php

namespace AppBundle\Form;

use AppBundle\Entity\Shipper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShipperType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('Quantity',NumberType::class,[
            'attr' => ['class' => 'form-control']
        ])
        ->add('Add to cart !',SubmitType::class,[
            'attr' => ['class' => 'btn btn-default']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Shipper::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_shipper_type';
    }
}
