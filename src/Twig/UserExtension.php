<?php

namespace App\Twig;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function getFunctions()
    {
        return array(
            new TwigFunction('getFullyLoggedUser', array($this, 'getFullyLoggedUser')),
        );
    }

    public function getFullyLoggedUser(User $user){

        $clase = 'App\Entity\\';

        switch ($user->getRoles()[0]){
            case 'ROLE_ADMIN':
                $clase .= 'Administrador';
                break;
            case 'ROLE_SPECIALIST':
                $clase .= 'Especialista';
                break;
            case 'ROLE_PATIENT':
                $clase .= 'Paciente';
                break;
        }

        return $this->entityManager->getRepository($clase)->findOneBy(array('usuario'=>$user));
    }
}
