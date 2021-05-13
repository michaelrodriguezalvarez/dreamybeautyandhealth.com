<?php

namespace App\Controller;

use App\Entity\Paquete;
use App\Entity\Paqueteservicio;
use App\Form\PaqueteType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/paquete")
 */
class PaqueteController extends AbstractController
{
    /**
     * @Route("/", name="paquete_index", methods={"GET"})
     */
    public function index(): Response
    {
        $paquetes = $this->getDoctrine()
            ->getRepository(Paquete::class)
            ->findAll();

        return $this->render('paquete/index.html.twig', [
            'paquetes' => $paquetes,
        ]);
    }

    /**
     * @Route("/new", name="paquete_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageUploader $imageUploader): Response
    {
        $paquete = new Paquete();
        $form = $this->createForm(PaqueteType::class, $paquete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $imagen = null;
            $fichero = $form->get('fichero')->getData();
            if ($fichero) {
                $description = 'Imagen del paquete ' . $paquete->getNombre();
                $imagen = $imageUploader->upload($fichero, $description);
            }
            $paquete->setImagen($imagen);
            $entityManager->persist($paquete);

            $servicios = $form->get('servicios')->getData();
            foreach ($servicios as $servicio){
                $paqueteServicio = new Paqueteservicio();
                $paqueteServicio->setPaquete($paquete);
                $paqueteServicio->setServicio($servicio);
                $entityManager->persist($paqueteServicio);
            }

            $entityManager->flush();

            $this->addFlash('notification', 'Paquete creado correctamente.');
            return $this->redirectToRoute('paquete_index');
        }

        return $this->render('paquete/new.html.twig', [
            'paquete' => $paquete,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="paquete_show", methods={"GET"})
     */
    public function show(Paquete $paquete): Response
    {
        $servicios = $this->getDoctrine()
            ->getRepository(Paqueteservicio::class)
            ->obtenerServiciosPaquete($paquete);
        return $this->render('paquete/show.html.twig', [
            'paquete' => $paquete,
            'servicios' => $servicios
        ]);
    }

    /**
     * @Route("/{id}/edit", name="paquete_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Paquete $paquete): Response
    {
        $form = $this->createForm(PaqueteType::class, $paquete);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $servicios = $form->get('servicios')->getData();

            $paquetesServiciosGuardados = $this->getDoctrine()
                ->getRepository(Paqueteservicio::class)->findBy(array('paquete'=>$paquete));

            $entityManager = $this->getDoctrine()->getManager();

            foreach ($paquetesServiciosGuardados as $paqueteServicioGuardado){
                $contador = 1;
                foreach ($servicios as $servicio) {
                    if($paqueteServicioGuardado->getServicio()->getId() == $servicio->getId()){
                        break;
                    }else if($paqueteServicioGuardado->getServicio()->getId() != $servicio->getId() && $contador == count($servicios)){
                        $entityManager->remove($paqueteServicioGuardado);
                    }
                    $contador = $contador + 1;
                }
            }

            foreach ($servicios as $servicio){
                $contador = 1;
                foreach ($paquetesServiciosGuardados as $paqueteServicioGuardado){
                    if($servicio->getId() == $paqueteServicioGuardado->getServicio()->getId()){
                        break;
                    }else if($servicio->getId() != $paqueteServicioGuardado->getServicio()->getId() && $contador == count($paquetesServiciosGuardados)){
                        $paqueteServicioNuevo = new Paqueteservicio();
                        $paqueteServicioNuevo->setPaquete($paquete);
                        $paqueteServicioNuevo->setServicio($servicio);
                        $entityManager->persist($paqueteServicioNuevo);
                    }
                    $contador = $contador + 1;
                }
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notification', 'Paquete editado correctamente.');
            return $this->redirectToRoute('paquete_index');
        }

        return $this->render('paquete/edit.html.twig', [
            'paquete' => $paquete,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="paquete_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Paquete $paquete): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paquete->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $foto = $paquete->getImagen();
            $entityManager->remove($paquete);
            if ($foto){
                unlink($this->getParameter('images_directory').'/'.$foto->getImagen());
                $entityManager->remove($foto);
            }
            $entityManager->flush();
            $this->addFlash('notification', 'Paquete eliminado correctamente.');
        }

        return $this->redirectToRoute('paquete_index');
    }
}
