<?php

namespace App\Twig;

use App\Entity\Cita;
use App\Entity\Paciente;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CitaExtension extends AbstractExtension{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function getFunctions()
    {
        return array(
            new TwigFunction('getCitasUsuario', array($this, 'getCitasUsuario')),
        );
    }

    public function getCitasUsuario(User $user){
        $citas = array();
        $rol = $user->getRoles()[0];

        if ($rol == 'ROLE_ADMIN' || $rol == 'ROLE_SPECIALIST'){
            $citas = $this->entityManager
                ->getRepository(Cita::class)
                ->findBy(array('estado' => array('Reservada', 'Reprogramada')));
        }elseif ($rol == 'ROLE_PATIENT'){
            /** @var Paciente $paciente */
            $paciente = $this->entityManager
                ->getRepository(Paciente::class)
                ->findOneBy(array('usuario'=>$user));
            $citas = $this->entityManager
                ->getRepository(Cita::class)
                ->findBy(array('paciente' => $paciente, 'estado' => array('Reservada', 'Reprogramada')));
        }

        return count($citas);
    }
}
