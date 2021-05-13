<?php

namespace App\EventListener;

use App\Entity\Paciente;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CitaEstadoSubscriber implements EventSubscriberInterface
{
    private $authorizationChecker;
    private $tokenStorage;
    private $entityManager;
    private $estados;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $autheticationManager, AccessDecisionManagerInterface $accessDecisionManager, EntityManagerInterface $entityManager, $estados)
    {
        $this->authorizationChecker = new AuthorizationChecker(
            $tokenStorage,
            $autheticationManager,
            $accessDecisionManager
        );

        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
        $this->estados = $estados;
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_SPECIALIST')){
            $form
                ->add('estado', ChoiceType::class, array(
                    'choices' => $this->estados,
                    'attr' => array('class' => 'form-control'),
                ));
        }elseif($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
            $form
                ->add('estado', HiddenType::class, array(
                    'required' => false
                ));
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $entidad = $event->getData();
        $entidad_original = $event->getForm()->getData();

        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')){
            $entidad['estado'] = ($entidad_original && null !== $entidad_original->getId()) ? $entidad_original['estado'] : 'Reservada' ;
            $event->setData($entidad);
        }
    }
}