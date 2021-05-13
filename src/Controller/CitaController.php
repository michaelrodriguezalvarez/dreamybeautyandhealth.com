<?php

namespace App\Controller;

use App\Entity\Cita;
use App\Form\CitaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cita")
 */
class CitaController extends AbstractController
{
    private $estados;

    public function __construct()
    {
        $this->estados = array(
            'Reservada' => 'Reservada',
            'Reprogramada' => 'Reprogramada',
            'Ejecutada' => 'Ejecutada',
            'Cancelada' => 'Cancelada'
        );
    }

    /**
     * @Route("/", name="cita_index", methods={"GET"})
     */
    public function index(): Response
    {
        $citas = $this->getDoctrine()
            ->getRepository(Cita::class)
            ->findAll();

        return $this->render('cita/index.html.twig', [
            'citas' => $citas,
        ]);
    }

    /**
     * @Route("/new", name="cita_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $citum = new Cita();
        $form = $this->createForm(CitaType::class, $citum, array('estados' => $this->estados));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($citum);
            $entityManager->flush();

            return $this->redirectToRoute('cita_index');
        }

        return $this->render('cita/new.html.twig', [
            'citum' => $citum,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="cita_show", methods={"GET"})
     */
    public function show(Cita $citum): Response
    {
        return $this->render('cita/show.html.twig', [
            'citum' => $citum,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="cita_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Cita $citum): Response
    {
        $form = $this->createForm(CitaType::class, $citum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('cita_index');
        }

        return $this->render('cita/edit.html.twig', [
            'citum' => $citum,
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

        return $this->redirectToRoute('cita_index');
    }
}
