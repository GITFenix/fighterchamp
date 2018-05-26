<?php

namespace AppBundle\Form\User;

use AppBundle\Form\EventListener\CreateCoachIfDosentExist;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FighterType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();

        $builder
            ->add('type', HiddenType::class, [
                'data' => 1])
            ->add('birthDay', BirthdayType::class, [
                'translation_domain' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('phone', TextType::class, [
                'constraints' => [new NotBlank()]
            ])
            ->add('users', EntityType::class, [
                'required' => false,
                'empty_data' => null,
                'class' => 'AppBundle:User',
//                'data' => $user ? $user->getCoach() : null,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->andWhere('u.type = 2')
                        ->orderBy('u.name', 'ASC');
                }])
            ->add('motherName', TextType::class, ['label' => 'Imię Matki'])
            ->add('fatherName', TextType::class, ['label' => 'Imię Ojca'])
            ->add('pesel', TextType::class, ['label' => 'Pesel']);

              $builder->addEventSubscriber(new CreateCoachIfDosentExist($this->em));
    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


    public function getParent()
    {
        return UserType::class;
    }

}
