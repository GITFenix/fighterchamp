<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class AdminSignUpTournamentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $trait = $options['trait_choices'];

        $builder
            ->add('formula', ChoiceType::class, [
                'choices'  => [
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C'
                ]])
            ->add('trainingTime', IntegerType::class)
            ->add('weight', ChoiceType::class, [
                'choices'  => $trait
                ])
            ->add('youtubeId', TextType::class)
            ->add('musicArtistAndTitle', TextType::class)

           ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SignUpTournament',
            'trait_choices' => null,
            'user_id' => null,
            'csrf_protection'   => false,
        ));
    }
}
