<?php

namespace App\Form;

use App\Entity\Paquete;
use App\EventListener\PaqueteImageSubscriber;
use App\EventListener\PaqueteServicioSubscriber;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaqueteType extends AbstractType
{
    private $imageUploader;
    private $entityManager;
    private $targetDirectory;

    public function __construct(ImageUploader $imageUploader, EntityManagerInterface $entityManager, $targetDirectory){
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
            ->add('descripcion', TextareaType::class, array(
                'label' => 'Descripción',
                'attr' => array('class'=>'form-control'),
            ))
            ->add('imagen', HiddenType::class, array(
                'data' => null
            ))
            ->addEventSubscriber(new PaqueteImageSubscriber($this->imageUploader, $this->entityManager, $this->targetDirectory, 'patient'))
            ->addEventSubscriber(new PaqueteServicioSubscriber($this->entityManager));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Paquete::class,
        ]);
    }
}
