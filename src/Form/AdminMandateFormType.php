<?php

namespace App\Form;

use App\Entity\Mandate;
use App\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminMandateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uid')
            ->add('title')
            ->add('description')
            ->add('desiredDate', DateType::class, [
                'widget' => "single_text"
            ])
            ->add('client_lastname', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "Lastname"
            ])
            ->add('client_firstname', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "Firstname"
            ])
            ->add('client_email', TextType::class, [
                'mapped' => false,
                'required' => true,
                'label' => "Email"
            ])
            ->add('client_company', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "Company"
            ])
            ->add('client_phonenumber', TextType::class, [
                'mapped' => false,
                'required' => false,
                'label' => "Phone number",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mandate::class,
        ]);
    }
}