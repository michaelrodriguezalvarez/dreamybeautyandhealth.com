<?php

namespace App\Form;

use App\Entity\Especialista;
use App\EventListener\UserImageSubscriber;
use App\EventListener\UserPasswordSubscriber;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EspecialistaType extends AbstractType
{
    private $passwordEncoder;
    private $imageUploader;
    private $entityManager;
    private $targetDirectory;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, ImageUploader $imageUploader, EntityManagerInterface $entityManager, $targetDirectory){
        $this->passwordEncoder = $passwordEncoder;
        $this->imageUploader = $imageUploader;
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array (
                'attr' => array('class'=>'form-control'),
            ))
            ->add('apellidos', TextType::class, array (
                'attr' => array('class'=>'form-control'),
            ));

        $builder
            ->add($builder->create('usuario', UserType::class, array(
                'label' => false,
                'by_reference' => true))
                ->add('email', EmailType::class, array(
                    'label'=>'Correo',
                    'attr' => array('class'=>'form-control'),
                    'required' => true
                ))
                ->addEventSubscriber(new UserPasswordSubscriber($this->passwordEncoder, 'specialist'))
            );

        $builder
            ->add('nacionalidad', TextType::class, array (
                'attr' => array('class'=>'form-control'),
            ))
            ->add('foto', HiddenType::class, array(
                'data' => null
            ))
            ->addEventSubscriber(new UserImageSubscriber($this->imageUploader, $this->entityManager, $this->targetDirectory, 'specialist'));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Especialista::class,
        ]);
    }
}
