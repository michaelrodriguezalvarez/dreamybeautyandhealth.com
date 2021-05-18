<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Entity\Especialidad;
use App\Entity\Especialista;
use App\Entity\Paciente;
use App\Entity\Paquete;
use App\Entity\Servicio;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class InicioController extends AbstractController
{
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $autheticationManager, AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->authorizationChecker = new AuthorizationChecker(
            $tokenStorage,
            $autheticationManager,
            $accessDecisionManager
        );
    }

    /**
     * @Route("/inicio", name="inicio")
     */
    public function index(): Response
    {
        if($this->authorizationChecker->isGranted('ROLE_PATIENT')){
            $cantUsuarios = $this->getDoctrine()
                ->getRepository(Paciente::class)->count(array());
            $cantEspecialistas = $this->getDoctrine()
                ->getRepository(Especialista::class)->count(array());
            $cantCitas = $this->getDoctrine()
                ->getRepository(Cita::class)->count(array());
            $cantServicios = $this->getDoctrine()
                ->getRepository(Servicio::class)->count(array());

            $servicios = $this->getDoctrine()
                ->getRepository(Servicio::class)->findAll();
            $paquetes = $this->getDoctrine()
                ->getRepository(Paquete::class)->findAll();
            $especialidades = $this->getDoctrine()
                ->getRepository(Especialidad::class)->findAll();

            return $this->render('inicio/ofertas.html.twig', [
                'cantUsuarios' => $cantUsuarios,
                'cantEspecialistas' => $cantEspecialistas,
                'cantCitas' => $cantCitas,
                'cantServicios' => $cantServicios,
                'servicios' => $servicios,
                'paquetes' => $paquetes,
                'especialidades' => $especialidades,
            ]);
        }

        return $this->render('inicio/index.html.twig', [
            'controller_name' => 'InicioController',
        ]);
    }
}
