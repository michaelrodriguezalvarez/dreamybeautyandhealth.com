<?php

namespace App\Controller;

use App\Entity\Servicio;
use App\Form\ServicioType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servicio")
 */
class ServicioController extends AbstractController
{
    /**
     * @Route("/", name="servicio_index", methods={"GET"})
     */
    public function index(): Response
    {
        $servicios = $this->getDoctrine()
            ->getRepository(Servicio::class)
            ->findAll();

        return $this->render('servicio/index.html.twig', [
            'servicios' => $servicios,
        ]);
    }

    /**
     * @Route("/new", name="servicio_new", methods={"GET","POST"})
     */
    public function new(Request $request,  ImageUploader $imageUploader): Response
    {
        $servicio = new Servicio();
        $form = $this->createForm(ServicioType::class, $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $imagen = null;
            $fichero = $form->get('fichero')->getData();
            if ($fichero) {
                $description = 'Imagen del servicio ' . $servicio->getNombre();
                $imagen = $imageUploader->upload($fichero, $description);
            }
            $servicio->setImagen($imagen);
            $entityManager->persist($servicio);
            $entityManager->flush();

            $this->addFlash('notification', 'Servicio creado correctamente.');
            return $this->redirectToRoute('servicio_index');
        }

        return $this->render('servicio/new.html.twig', [
            'servicio' => $servicio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="servicio_show", methods={"GET"})
     */
    public function show(Servicio $servicio): Response
    {
        return $this->render('servicio/show.html.twig', [
            'servicio' => $servicio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="servicio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Servicio $servicio): Response
    {
        $form = $this->createForm(ServicioType::class, $servicio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notification', 'Servicio editado correctamente.');
            return $this->redirectToRoute('servicio_index');
        }

        return $this->render('servicio/edit.html.twig', [
            'servicio' => $servicio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="servicio_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Servicio $servicio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$servicio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $foto = $servicio->getImagen();
            $entityManager->remove($servicio);
            if ($foto){
                unlink($this->getParameter('images_directory').'/'.$foto->getImagen());
                $entityManager->remove($foto);
            }
            $entityManager->flush();
            $this->addFlash('notification', 'Servicio eliminado correctamente.');
        }

        return $this->redirectToRoute('servicio_index');
    }
}
