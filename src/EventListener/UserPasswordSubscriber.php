<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordSubscriber implements EventSubscriberInterface
{
    private $passwordEncoder;
    private $role;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, $role) {
        $this->passwordEncoder = $passwordEncoder;
        $this->role = $role;
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
        $user = $event->getData();
        $form = $event->getForm();

        $requiredPasswordField = false;
        $helpPasswordField = 'Deje el campo clave en blanco para mantener su valor actual';

        if (!$user || null === $user->getId()) {
            $requiredPasswordField = true;
            $helpPasswordField = '';
        }

        if ($this->role == 'patient'){
            $form
                ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class'=>'form-control')),
                    'required' => $requiredPasswordField,
                    'first_options' => array('label'=>'Clave', 'help'=>$helpPasswordField),
                    'second_options' => array('label'=>'Confirmar clave'),
                ));
        }else {
            $form
                ->add('password', PasswordType::class, array(
                    'label'=>'Clave',
                    'help'=>$helpPasswordField,
                    'attr' => array('class'=>'form-control'),
                    'required' => $requiredPasswordField,
                ));
        }
    }

    public function preSubmit(FormEvent $event)
    {
        $user = $event->getData();

        $usuario_original = $event->getForm()->getData();
        if ($usuario_original && null !== $usuario_original->getId()) {
            if ($this->role == 'patient'){
                if ($user['password']['first'] == ''){
                    $user['password']['first'] = $usuario_original->getPassword();
                    $user['password']['second'] = $usuario_original->getPassword();
                }else{
                    $user['password']['first'] = $this->passwordEncoder->encodePassword($usuario_original, $user['password']['first']);
                    $user['password']['second'] = $this->passwordEncoder->encodePassword($usuario_original, $user['password']['first']);
                }
            }else{
                if ($user['password'] == ''){
                    $user['password'] = $usuario_original->getPassword();
                }else{
                    $user['password'] = $this->passwordEncoder->encodePassword($usuario_original, $user['password']);
                }
            }

            $event->setData($user);
        }
    }
}