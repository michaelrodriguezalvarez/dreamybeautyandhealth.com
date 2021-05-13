<?php

namespace App\Form;

use App\Entity\Cita;
use App\Entity\Especialidad;
use App\Entity\Paquete;
use App\Entity\Servicio;
use App\EventListener\CitaEstadoSubscriber;
use App\EventListener\CitaPacienteSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class CitaType extends AbstractType
{
    private $tokenStorage;
    private $autheticationManager;
    private $accessDecisionManager;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $autheticationManager, AccessDecisionManagerInterface $accessDecisionManager, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->autheticationManager = $autheticationManager;
        $this->accessDecisionManager = $accessDecisionManager;
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $estados = $options['estados'];

        $builder
            ->add('fecha', DateTimeType::class, array(
                'label' => 'Fecha',
                'widget' => 'single_text',
                'attr' => array(
                    'class' => 'js-datepicker form-control'
                ),
            ));
            $builder
            ->addEventSubscriber(new CitaPacienteSubscriber($this->tokenStorage, $this->autheticationManager, $this->accessDecisionManager, $this->entityManager))
            ->addEventSubscriber(new CitaEstadoSubscriber($this->tokenStorage, $this->autheticationManager, $this->accessDecisionManager, $this->entityManager, $estados));

        $builder
            ->add('tipo', ChoiceType::class, array(
                'label' => 'Tipo',
                'help' => 'El Tipo se refiere al servicio que usted requiere solicitar',
                'attr' => array('class' => 'form-control'),
                'mapped' => false,
                'required' => true,
                'expanded' => true,
                'choices' => array(
                    'Especialidad' => 'Especialidad',
                    'Paquete' => 'Paquete',
                    'Servicio' => 'Servicio',
                ),
                'data' => 'Especialidad',
            ))
            ->add('especialidad', EntityType::class, array(
                'class' => Especialidad::class,
                'choice_label' => 'nombre',
                'attr' => array('class'=>'form-control'),
                'required' => false,
                'placeholder' => '--Seleccione--',
            ))
            ->add('paquete', EntityType::class, array(
                'class' => Paquete::class,
                'choice_label' => 'nombre',
                'attr' => array('class'=>'form-control'),
                'required' => false,
                'placeholder' => '--Seleccione--',
            ))
            ->add('servicio', EntityType::class, array(
                'class' => Servicio::class,
                'choice_label' => 'nombre',
                'attr' => array('class'=>'form-control'),
                'required' => false,
                'placeholder' => '--Seleccione--',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Cita::class,
            'estados' => 'estados'
        ]);
    }
}
