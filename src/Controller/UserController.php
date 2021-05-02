<?php

namespace App\Controller;

use App\Entity\Administrador;
use App\Entity\Especialista;
use App\Entity\Paciente;
use App\Entity\User;
use App\Entity\Imagen;
use App\Form\AdministradorType;
use App\Form\EspecialistaType;
use App\Form\PacienteType;
use App\Form\UserType;
use App\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $passwordEncoder;
    private $authorizationChecker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $autheticationManager, AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->authorizationChecker = new AuthorizationChecker(
            $tokenStorage,
            $autheticationManager,
            $accessDecisionManager
        );
    }

    /**
     * @Route("/index/{role}", name="user_index", methods={"GET"})
     */
    public function index(string $role): Response
    {
        $users = array();
        $clase = 'App\Entity\\';

        switch ($role){
            case 'admin':
                $clase .= 'Administrador';
                break;
            case 'specialist':
                $clase .= 'Especialista';
                break;
            case 'patient':
                $clase .= 'Paciente';
                break;
        }
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository($clase)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'role' => $role,
        ]);
    }

    /**
     * @Route("/new/{role}", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, string $role, ImageUploader $imageUploader): Response
    {
        $user = new User();
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            if($role == 'admin'){
                $administrador = new Administrador();
                $form = $this->createForm(AdministradorType::class, $administrador);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager = $this->getDoctrine()->getManager();

                    $imagen = null;
                    $fichero = $form->get('fichero')->getData();
                    if ($fichero){
                        $description = 'Foto del administrador '. $administrador->getNombre(). ' '. $administrador->getApellidos();
                        $imagen = $imageUploader->upload($fichero, $description);
                    }
                    $administrador->setFoto($imagen);
                    $administrador->getUsuario()->setRoles(array('ROLE_ADMIN'));
                    $administrador->getUsuario()->setPassword($this->passwordEncoder->encodePassword($administrador->getUsuario(), $administrador->getUsuario()->getPassword()));
                    $entityManager->persist($administrador->getUsuario());
                    $entityManager->persist($administrador);
                    $entityManager->flush();
                    return $this->redirectToRoute('user_index', array('role'=>$role));
                }

                return $this->render('user/new.html.twig', [
                    'administrador' => $administrador,
                    'form' => $form->createView(),
                    'role' => $role,
                ]);
            }else {
                if ($role == 'specialist'){
                    $especialista = new Especialista();
                    $form = $this->createForm(EspecialistaType::class, $especialista);
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $entityManager = $this->getDoctrine()->getManager();
                        $imagen = null;
                        $fichero = $form->get('fichero')->getData();
                        if ($fichero){
                            $description = 'Foto del especialista '. $especialista->getNombre(). ' '. $especialista->getApellidos();
                            $imagen = $imageUploader->upload($fichero, $description);
                        }
                        $especialista->setFoto($imagen);
                        $especialista->getUsuario()->setRoles(array('ROLE_SPECIALIST'));
                        $especialista->getUsuario()->setPassword($this->passwordEncoder->encodePassword($especialista->getUsuario(), $especialista->getUsuario()->getPassword()));
                        $entityManager->persist($especialista->getUsuario());
                        $entityManager->persist($especialista);
                        $entityManager->flush();
                        return $this->redirectToRoute('user_index', array('role'=>$role));
                    }

                    return $this->render('user/new.html.twig', [
                        'especialista' => $especialista,
                        'form' => $form->createView(),
                        'role' => $role,
                    ]);
                }else{
                    if($role == 'patient'){
                        $paciente = new Paciente();
                        $form = $this->createForm(PacienteType::class, $paciente);
                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {
                            $entityManager = $this->getDoctrine()->getManager();
                            $imagen = null;
                            $fichero = $form->get('fichero')->getData();
                            if ($fichero){
                                $description = 'Foto del paciente '. $paciente->getNombre(). ' '. $paciente->getApellidos();
                                $imagen = $imageUploader->upload($fichero, $description);
                            }
                            $paciente->setFoto($imagen);
                            $paciente->getUsuario()->setRoles(array('ROLE_PATIENT'));
                            $paciente->getUsuario()->setPassword($this->passwordEncoder->encodePassword($paciente->getUsuario(), $paciente->getUsuario()->getPassword()));
                            $entityManager->persist($paciente->getUsuario());
                            $entityManager->persist($paciente);
                            $entityManager->flush();
                            return $this->redirectToRoute('user_index', array('role'=>$role));
                        }

                        return $this->render('user/new.html.twig', [
                            'paciente' => $paciente,
                            'form' => $form->createView(),
                            'role' => $role,
                        ]);
                    }
                }
            }
        }
        else {
            if(!$this->authorizationChecker->isGranted('ROLE_PATIENT') && !$this->authorizationChecker->isGranted('ROLE_SPECIALIST')){
                if($role == 'patient'){
                    $paciente = new Paciente();
                    $form = $this->createForm(PacienteType::class, $paciente);
                    $form->handleRequest($request);

                    if ($form->isSubmitted() && $form->isValid()) {
                        $entityManager = $this->getDoctrine()->getManager();
                        $imagen = null;
                        $fichero = $form->get('fichero')->getData();
                        if ($fichero){
                            $description = 'Foto del paciente '. $paciente->getNombre(). ' '. $paciente->getApellidos();
                            $imagen = $imageUploader->upload($fichero, $description);
                        }
                        $paciente->setFoto($imagen);
                        $paciente->getUsuario()->setRoles(array('ROLE_PATIENT'));
                        $paciente->getUsuario()->setPassword($this->passwordEncoder->encodePassword($paciente->getUsuario(), $paciente->getUsuario()->getPassword()));
                        $entityManager->persist($paciente->getUsuario());
                        $entityManager->persist($paciente);
                        $entityManager->flush();
                        $this->addFlash('notification', 'Usuario registrado correctamente.');
                        return $this->redirectToRoute('user_new', array('role'=>$role));
                    }

                    return $this->render('user/new.html.twig', [
                        'paciente' => $paciente,
                        'form' => $form->createView(),
                        'role' => $role,
                    ]);
                }
            }
        }

        throw new AccessDeniedException("Access Denied.");
    }

    /**
     * @Route("/{id}/{role}", name="user_show", methods={"GET"})
     */
    public function show(User $user, string $role): Response
    {
        $clase = 'App\Entity\\';

        switch ($role){
            case 'admin':
                $clase .= 'Administrador';
                break;
            case 'specialist':
                $clase .= 'Especialista';
                break;
            case 'patient':
                $clase .= 'Paciente';
                break;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository($clase)->findOneBy(array('usuario'=>$user));

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'role' => $role,
        ]);
    }

    /**
     * @Route("/{id}/edit/{role}", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, string $role): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = null;
        switch ($role){
            case 'admin':
                $user = $entityManager->getRepository('App\Entity\Administrador')->findOneBy(array('usuario'=>$user));
                $form = $this->createForm(AdministradorType::class, $user);
                break;
            case 'specialist':
                $user = $entityManager->getRepository('App\Entity\Especialista')->findOneBy(array('usuario'=>$user));
                $form = $this->createForm(EspecialistaType::class, $user);
                break;
            case 'patient':
                $user = $entityManager->getRepository('App\Entity\Paciente')->findOneBy(array('usuario'=>$user));
                $form = $this->createForm(PacienteType::class, $user);
                break;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                return $this->redirectToRoute('user_index', array('role' => $role));
            }else {
                $this->addFlash('notification', 'Usuario editado correctamente.');
                return $this->redirectToRoute('user_edit', array('id' => $user->getUsuario()->getId(), 'role' => $role));
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'role' => $role,
        ]);
    }

    /**
     * @Route("/{id}/{role}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user, string $role): Response
    {
        $clase = 'App\Entity\\';

        switch ($role){
            case 'admin':
                $clase .= 'Administrador';
                break;
            case 'specialist':
                $clase .= 'Especialista';
                break;
            case 'patient':
                $clase .= 'Paciente';
                break;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository($clase)->findOneBy(array('usuario'=>$user));

        $user_logged = $this->getUser();

        if ($user->getUsuario()->getId() != $user_logged->getId()){
            if ($this->isCsrfTokenValid('delete'.$user->getUsuario()->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $foto = $user->getFoto();
                $usuario = $user->getUsuario();
                $entityManager->remove($user);
                $entityManager->remove($usuario);
                if ($foto){
                    unlink($this->getParameter('images_directory').'/'.$foto->getImagen());
                    $entityManager->remove($foto);
                }
                $entityManager->flush();
            }
        }else{
            $this->addFlash('notification', 'El usuario authenticado no se puede eliminar.');
        }

        return $this->redirectToRoute('user_index', array('role'=>$role));
    }
}
