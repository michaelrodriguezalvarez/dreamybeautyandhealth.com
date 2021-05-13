<?php

namespace App\EventListener;

use App\Entity\Paqueteservicio;
use App\Entity\Servicio;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PaqueteServicioSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $entidad = $event->getData();
        $form = $event->getForm();
        $servicios = array();

        if ($entidad && null === $entidad->getId()) {

        }else{
            $servicios = $this->entityManager
                ->getRepository(Paqueteservicio::class)
                ->obtenerServiciosPaquete($entidad);
        }

        $form
            ->add('servicios', EntityType::class, array(
            'class' => Servicio::class,
            'choice_label' => 'nombre',
            'attr' => array('class'=>'form-control'),
            'data' => $servicios,
            'required' => true,
            'mapped' => false,
            'multiple' => true
        ));
    }
}