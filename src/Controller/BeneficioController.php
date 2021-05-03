<?php

namespace App\Controller;

use App\Entity\Beneficio;
use App\Form\BeneficioType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/beneficio")
 */
class BeneficioController extends AbstractController
{
    /**
     * @Route("/", name="beneficio_index", methods={"GET"})
     */
    public function index(): Response
    {
        $beneficios = $this->getDoctrine()
            ->getRepository(Beneficio::class)
            ->findAll();

        return $this->render('beneficio/index.html.twig', [
            'beneficios' => $beneficios,
        ]);
    }

    /**
     * @Route("/new", name="beneficio_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageUploader $imageUploader): Response
    {
        $beneficio = new Beneficio();
        $form = $this->createForm(BeneficioType::class, $beneficio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $imagen = null;
            $fichero = $form->get('fichero')->getData();
            if ($fichero){
                $description = 'Imagen del beneficio '. $beneficio->getNombre();
                $imagen = $imageUploader->upload($fichero, $description);
            }
            $beneficio->setImagen($imagen);

            $entityManager->persist($beneficio);
            $entityManager->flush();

            $this->addFlash('notification', 'Beneficio creado correctamente.');
            return $this->redirectToRoute('beneficio_index');
        }

        return $this->render('beneficio/new.html.twig', [
            'beneficio' => $beneficio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="beneficio_show", methods={"GET"})
     */
    public function show(Beneficio $beneficio): Response
    {
        return $this->render('beneficio/show.html.twig', [
            'beneficio' => $beneficio,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="beneficio_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Beneficio $beneficio): Response
    {
        $form = $this->createForm(BeneficioType::class, $beneficio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('notification', 'Beneficio editado correctamente.');
            return $this->redirectToRoute('beneficio_index');
        }

        return $this->render('beneficio/edit.html.twig', [
            'beneficio' => $beneficio,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="beneficio_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Beneficio $beneficio): Response
    {
        if ($this->isCsrfTokenValid('delete'.$beneficio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $foto = $beneficio->getImagen();
            $entityManager->remove($beneficio);
            if ($foto){
                unlink($this->getParameter('images_directory').'/'.$foto->getImagen());
                $entityManager->remove($foto);
            }
            $entityManager->flush();
            $this->addFlash('notification', 'Beneficio eliminado correctamente.');
        }

        return $this->redirectToRoute('beneficio_index');
    }
}
