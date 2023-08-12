<?php

namespace App\Form;

use App\Entity\Games;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserGamesProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('myGames', EntityType::class, [
                'class' => Games::class,
                'choice_label' => 'name',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Jeux',
                'attr' => [
                    'class' => 'form-control select-multiple',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
