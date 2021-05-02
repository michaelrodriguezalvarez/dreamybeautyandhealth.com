<?php

namespace App\Service;

use App\Entity\Imagen;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader{
    private $targetDirectory;
    private $slugger;
    private $entityManager;


    public function __construct($targetDirectory, SluggerInterface $slugger, EntityManagerInterface $entityManager)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->entityManager = $entityManager;
    }

    public function upload(UploadedFile $file, $imagenDescription): Imagen{

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $filename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $filename);
            $imagen = new Imagen();
            $imagen->setImagen($filename);
            $imagen->setNombre($originalFilename);
            $imagen->setDescripcion($imagenDescription);
            $this->entityManager->persist($imagen);
            $this->entityManager->flush();
            return $imagen;

        } catch (FileException $e) {
            return new Imagen();
        }
    }

    public function getTargetDirectory(){
        return $this->targetDirectory;
    }
}