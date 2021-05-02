<?php

namespace App\EventListener;

use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Image;

class UserImageSubscriber implements EventSubscriberInterface
{
    private $imageUploader;
    private $entityManager;
    private $targetDirectory;
    private $role;

    public function __construct(ImageUploader $imageUploader, EntityManagerInterface $entityManager, $targetDirectory, $role){
        $this->imageUploader = $imageUploader;
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
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
        $administrador = $event->getData();
        $form = $event->getForm();

        $helpFicheroField = 'No seleccione ningún archivo para mantener la foto actual';

        if (!$administrador || null === $administrador->getId()) {
            $helpFicheroField = '';
        }

        $form
            ->add('fichero', FileType::class, array (
                'label' => 'Foto',
                'attr' => array('class'=>'form-control'),
                'help' => $helpFicheroField,
                'mapped' => false,
                'required' => false,
                'constraints' => array (
                    new Image(array(
                        'maxSize' => '1024k',
                    ))
                )
            ));
    }

    public function preSubmit(FormEvent $event)
    {
        $administrador = $event->getData();
        $administrador_original = $event->getForm()->getData();
        if ($administrador_original && null !== $administrador_original->getId()) {
            if ($administrador['fichero'] == null){
                if ($administrador_original->getFoto() != null){
                    $administrador['foto'] = $administrador_original->getFoto();
                }else{
                    $administrador['foto'] = null;
                }
            }else{
                $imagen = null;
                $fichero = $administrador['fichero'];

                $role_user = null;

                switch ($this->role){
                    case 'admin':
                        $role_user = 'administrador';
                        break;
                    case 'specialist':
                        $role_user = 'especialista';
                        break;
                    case 'patient':
                        $role_user = 'paciente';
                        break;
                }

                if ($fichero){
                    $description = 'Foto del '.$role_user.' '. $administrador['nombre']. ' '. $administrador['apellidos'];
                    $imagen = $this->imageUploader->upload($fichero, $description);

                }

                $foto = $administrador_original->getFoto();
                unlink($this->targetDirectory.'/'.$foto->getImagen());
                $this->entityManager->remove($foto);

                $administrador['fichero'] = $imagen;
                $administrador['foto'] = $imagen;
            }

            $event->setData($administrador);
        }
    }
}