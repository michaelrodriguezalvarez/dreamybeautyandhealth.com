<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Entity\Especialidad;
use App\Entity\Paciente;
use App\Entity\Paquete;
use App\Entity\Servicio;
use App\Form\CitaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * @Route("/cita")
 */
class CitaController extends AbstractController
{
    private $estados;
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $autheticationManager, AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->estados = array(
            'Solicitada' => 'Solicitada',
            'Reservada' => 'Reservada',
            'Reprogramada' => 'Reprogramada',
            'Ejecutada' => 'Ejecutada',
            'Cancelada' => 'Cancelada'
        );
        $this->authorizationChecker = new AuthorizationChecker(
            $tokenStorage,
            $autheticationManager,
            $accessDecisionManager
        );
    }

    /**
     * @Route("/", name="cita_index", methods={"GET"})
     */
    public function index(): Response
    {
        $citas = array();
        if($this->authorizationChecker->isGranted('ROLE_PATIENT')){
            /** @var User $user_logged */
            $user_logged = $this->getUser();
            /** @var Paciente $paciente */
            $paciente = $this->getDoctrine()
                ->getRepository(Paciente::class)
                ->findOneBy(array('usuario'=>$user_logged));
            $citas = $this->getDoctrine()
                ->getRepository(Cita::class)
                ->findBy(array('paciente' => $paciente));
        }elseif($this->authorizationChecker->isGranted('ROLE_ADMIN') || $this->authorizationChecker->isGranted('ROLE_SPECIALIST')){
            $citas = $this->getDoctrine()
                ->getRepository(Cita::class)
                ->findAll();
        }


        return $this->render('cita/index.html.twig', [
            'citas' => $citas,
        ]);
    }

    /**
     * @Route("/new", name="cita_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $cita = new Cita();
        $form = $this->createForm(CitaType::class, $cita, array('estados' => $this->estados));
        $form->handleRequest($request);
        $tipo = 'Especialidad';

        if ($form->isSubmitted() && $form->isValid()) {
            $tipo = $request->request->get('cita')['tipo'];
            $tieneError = false;

            switch ($tipo) {
                case 'Especialidad':
                    if($cita->getEspecialidad() == null){
                        $this->addFlash(
                            'notification',
                            'Debe seleccionar una Especialidad para la Cita.'
                        );
                        $tieneError = true;
                    }
                    break;
                case 'Paquete':
                    if($cita->getPaquete() == null){
                        $this->addFlash(
                            'notification',
                            'Debe seleccionar un Paquete para la Cita.'
                        );
                        $tieneError = true;
                    }
                    break;
                case 'Servicio':
                    if($cita->getServicio() == null){
                        $this->addFlash(
                            'notification',
                            'Debe seleccionar un Servicio para la Cita.'
                        );
                        $tieneError = true;
                    }
                    break;
            }

            if($tieneError == false){
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($cita);
                $entityManager->flush();

                $this->addFlash('notification', 'Cita creada correctamente.');
                return $this->redirectToRoute('cita_index');
            }
        }

        return $this->render('cita/new.html.twig', [
            'cita' => $cita,
            'form' => $form->createView(),
            'tipo' => $tipo,
        ]);
    }

    /**
     * @Route("/{id}", name="cita_show", methods={"GET"})
     */
    public function show(Cita $citum): Response
    {
        return $this->render('cita/show.html.twig', [
            'cita' => $citum,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cita_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cita $cita): Response
    {
        $form = $this->createForm(CitaType::class, $cita, array('estados' => $this->estados));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notification', 'Cita editada correctamente.');
            return $this->redirectToRoute('cita_index');
        }

        return $this->render('cita/edit.html.twig', [
            'cita' => $cita,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cita_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Cita $citum): Response
    {
        if ($this->isCsrfTokenValid('delete'.$citum->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $citum->setEstado($this->estados['Cancelada']);
            //$entityManager->remove($citum); // No se borran las citas
            $entityManager->flush();
        }

        $this->addFlash('notification', 'Cita cancelada correctamente.');
        return $this->redirectToRoute('cita_index');
    }

    /**
     * @Route("/solicitar/{tipo}/{id}", name="cita_solicitar", methods={"GET","POST"})
     */
    public function solicitar(Request $request, $tipo, $id): Response
    {
        $especialidad = null;
        $paquete = null;
        $servicio = null;

        switch ($tipo){
            case 'especialidad':
                $especialidad = $this->getDoctrine()
                    ->getRepository(Especialidad::class)->findOneBy(array('id'=>$id));
                break;
            case 'paquete':
                $paquete = $this->getDoctrine()
                    ->getRepository(Paquete::class)->findOneBy(array('id'=>$id));
                break;
            case 'servicio':
                $servicio = $this->getDoctrine()
                    ->getRepository(Servicio::class)->findOneBy(array('id'=>$id));
                break;
        }

        if ($especialidad == null && $paquete == null && $servicio == null) {
            throw new NotFoundHttpException(
                "El recurso solicitado no fue encontrado. Contacte con el administrador del sistema."
            );
        }

        if ($request->getMethod() == 'POST'){
            if($this->isCsrfTokenValid('solicitar'.$tipo.$id, $request->request->get('_token'))){
                if ($request->request->get('fecha')){
                    $user_logged = $this->getUser();
                    $paciente = $this->getDoctrine()
                        ->getRepository(Paciente::class)
                            ->findOneBy(array('usuario'=>$user_logged->getId()));
                    $cita = new Cita();
                    $cita->setFecha(new \DateTime($request->request->get('fecha')));
                    $cita->setEstado($this->estados['Solicitada']);
                    $cita->setEspecialidad($especialidad);
                    $cita->setPaciente($paciente);
                    $cita->setPaquete($paquete);
                    $cita->setServicio($servicio);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($cita);
                    $entityManager->flush();

                    $this->addFlash('notification', 'Cita creada correctamente.');
                    return $this->redirectToRoute('cita_index');
                }else{
                    $this->addFlash('notification', 'Usted debe especificar una fecha para la cita.');
                }
            }
        }

        return $this->render('cita/solicitar.html.twig', [
            'tipo' => $tipo,
            'id' => $id,
            'especialidad' => $especialidad,
            'paquete' => $paquete,
            'servicio' => $servicio,
        ]);
    }
}
