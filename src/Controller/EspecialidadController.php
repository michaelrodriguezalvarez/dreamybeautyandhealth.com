<?php

namespace App\Controller;

use App\Entity\Especialidad;
use App\Form\EspecialidadType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/especialidad")
 */
class EspecialidadController extends AbstractController
{
    /**
     * @Route("/", name="especialidad_index", methods={"GET"})
     */
    public function index(): Response
    {
        $especialidads = $this->getDoctrine()
            ->getRepository(Especialidad::class)
            ->findAll();

        return $this->render('especialidad/index.html.twig', [
            'especialidads' => $especialidads,
        ]);
    }

    /**
     * @Route("/new", name="especialidad_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageUploader $imageUploader): Response
    {
        $especialidad = new Especialidad();
        $form = $this->createForm(EspecialidadType::class, $especialidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $imagen = null;
            $fichero = $form->get('fichero')->getData();
            if ($fichero){
                $description = 'Imagen de la especialidad '. $especialidad->getNombre();
                $imagen = $imageUploader->upload($fichero, $description);
            }
            $especialidad->setImagen($imagen);

            $entityManager->persist($especialidad);
            $entityManager->flush();

            $this->addFlash('notification', 'Especialidad creada correctamente.');
            return $this->redirectToRoute('especialidad_index');
        }

        return $this->render('especialidad/new.html.twig', [
            'especialidad' => $especialidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="especialidad_show", methods={"GET"})
     */
    public function show(Especialidad $especialidad): Response
    {
        return $this->render('especialidad/show.html.twig', [
            'especialidad' => $especialidad,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="especialidad_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Especialidad $especialidad): Response
    {
        $form = $this->createForm(EspecialidadType::class, $especialidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notification', 'Especialidad editada correctamente.');
            return $this->redirectToRoute('especialidad_index');
        }

        return $this->render('especialidad/edit.html.twig', [
            'especialidad' => $especialidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="especialidad_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Especialidad $especialidad): Response
    {
        if ($this->isCsrfTokenValid('delete'.$especialidad->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($especialidad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('especialidad_index');
    }
}
