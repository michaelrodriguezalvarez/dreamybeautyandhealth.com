<?php

namespace App\EventListener;

use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Image;

class ServicioImageSubscriber implements EventSubscriberInterface
{
    private $imageUploader;
    private $entityManager;
    private $targetDirectory;

    public function __construct(ImageUploader $imageUploader, EntityManagerInterface $entityManager, $targetDirectory){
        $this->imageUploader = $imageUploader;
        $this->entityManager = $entityManager;
        $this->targetDirectory = $targetDirectory;
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
        $entidad = $event->getData();
        $form = $event->getForm();

        $helpFicheroField = 'No seleccione ningún archivo para mantener la imagen actual';

        if (!$entidad || null === $entidad->getId()) {
            $helpFicheroField = '';
        }

        $form
            ->add('fichero', FileType::class, array (
                'label' => 'Imagen',
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
        $entidad = $event->getData();
        $entidad_original = $event->getForm()->getData();
        if ($entidad_original && null !== $entidad_original->getId()) {
            if ($entidad['fichero'] == null){
                if ($entidad_original->getImagen() != null){
                    $entidad['imagen'] = $entidad_original->getImagen();
                }else{
                    $entidad['imagen'] = null;
                }
            }else{
                $imagen = null;
                $fichero = $entidad['fichero'];

                if ($fichero){
                    $description = 'Imagen del servicio '. $entidad['nombre'];
                    $imagen = $this->imageUploader->upload($fichero, $description);
                }

                $foto = $entidad_original->getImagen();
                if ($foto){
                    unlink($this->targetDirectory.'/'.$foto->getImagen());
                    $this->entityManager->remove($foto);
                }

                $entidad['fichero'] = $imagen;
                $entidad['imagen'] = $imagen;
            }

            $event->setData($entidad);
        }
    }
}