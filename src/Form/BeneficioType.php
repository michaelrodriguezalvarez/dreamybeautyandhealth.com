<?php

namespace App\Form;

use App\Entity\Beneficio;
use App\Entity\Servicio;
use App\EventListener\BeneficioImageSubscriber;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BeneficioType extends AbstractType
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
            ->add('nombre', TextType::class, array(
                'label'=>'Nombre',
                'attr' => array('class'=>'form-control')
            ))
            ->add('servicio', EntityType::class, array(
                'class' => Servicio::class,
                'choice_label' => 'nombre',
                'attr' => array('class'=>'form-control'),
                'required' => true
            ))
            ->add('descripcion', TextareaType::class, array(
                'label'=>'Descripción',
                'attr' => array('class'=>'form-control')
            ))
        ;

        $builder
            ->add('imagen', HiddenType::class, array(
                'data' => null
            ))
            ->addEventSubscriber(new BeneficioImageSubscriber($this->imageUploader, $this->entityManager, $this->targetDirectory));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Beneficio::class,
        ]);
    }
}
