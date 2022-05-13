<?php

namespace App\Form;

use App\Entity\Project;
use App\Repository\PersonRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectFormType extends AbstractType
{

    public function __construct(
        private PersonRepository $personRepository,
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('specificationsEndDate', null, [
                'widget' => 'single_text'
            ])
            ->add('state', ChoiceType::class, [
                'choices' => array_flip(Project::STATE_LABEL)
            ])
            ->add('coach', null, [
                'choices' => $this->getCoaches()
            ])
            ->add('class')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }

    private function getCoaches(): array
    {
        return $this->personRepository->findAllCoach();
    }
}
