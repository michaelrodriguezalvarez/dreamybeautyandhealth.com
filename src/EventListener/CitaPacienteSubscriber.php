<?php

namespace App\EventListener;

use App\Entity\Paciente;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class CitaPacienteSubscriber implements EventSubscriberInterface
{
    private $authorizationChecker;
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $autheticationManager, AccessDecisionManagerInterface $accessDecisionManager, EntityManagerInterface $entityManager)
    {
        $this->authorizationChecker = new AuthorizationChecker(
            $tokenStorage,
            $autheticationManager,
            $accessDecisionManager
        );

        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
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
                ->add('paciente', EntityType::class, array(
                    'class' => Paciente::class,
                    'choice_label' => function($paciente, $key, $index){
                        /** @var Paciente $paciente */
                        return $paciente->getNombre().' '.$paciente->getApellidos();
                    },
                    'attr' => array('class'=>'form-control'),
                    'required' => true
                ));
        }elseif($this->authorizationChecker->isGranted('ROLE_PATIENT')) {
                $form
                    ->add('paciente', HiddenType::class, array(
                        'required' => false
                    ));
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $entidad = $event->getData();

        if ($this->authorizationChecker->isGranted('ROLE_PATIENT')){
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            $paciente = $this->entityManager->getRepository(Paciente::class)->findOneBy(array('usuario'=>$user));
            $entidad['paciente'] = $paciente;
            $event->setData($entidad);
        }
    }
}
