<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\RoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserFormType extends AbstractType
{
    public function __construct(private RoleRepository $roleRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('isVerified')
            ->add('person', AdminPersonFormType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function getChoices(): array
    {
        $output = [];

        foreach ($this->roleRepository->findAll() as $role) {
            $output[$role->getName()] = (string)$role->getId();
        }

        return $output;
    }
}
