<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use function PHPSTORM_META\type;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Password',
                        'attr' => ['class' => 'form-control'],
                        'label_attr' => ['class' => 'col-sm-4 control-label']),
                    'second_options' => array('label' => 'Repeat Password',
                        'attr' => ['class' => 'form-control'],
                        'label_attr' => ['class' => 'col-sm-4 control-label']),
                    'invalid_message' => 'Password mismatch!'
                )
            )
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'First Name',
                'label_attr' => ['class' => 'col-sm-4 control-label']
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'label_attr' => ['class' => 'col-sm-4 control-label'],
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
