<?php

namespace App\Form;

use App\Entity\Especialidad;
use App\Entity\Servicio;
use App\EventListener\ServicioImageSubscriber;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicioType extends AbstractType
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
            ->add('nombre',TextType::class, array(
                'label'=>'Nombre',
                'attr' => array('class'=>'form-control')
            ))
            ->add('descripcion', TextareaType::class, array(
                'label'=>'Descripción',
                'attr' => array('class'=>'form-control')
            ))
            ->add('especialidad', EntityType::class, array(
                'class' => Especialidad::class,
                'choice_label' => 'nombre',
                'attr' => array('class'=>'form-control'),
                'required' => true
            ))
        ;
        $builder
            ->add('imagen', HiddenType::class, array(
                'data' => null
            ))
            ->addEventSubscriber(new ServicioImageSubscriber($this->imageUploader, $this->entityManager, $this->targetDirectory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Servicio::class,
        ]);
    }
}
