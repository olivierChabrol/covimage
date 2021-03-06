<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserRegisterType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Psr\Log\LoggerInterface;

/**
 * @Route("")
 */
class UserController extends AbstractController
{
    private $username;
    private function setUserName() {
        $user = $this->getUser();
        if (!$user) {
            $this->username = "anonymous";
        } else {
            $this->username = $user->getEmail();
        }

    }
    /**
     * @Route("/register/", name="user_registre_fe", methods={"GET","POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword($user,$user->getPassword()));
            $user->setRoles($user->getRoles());
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, LoggerInterface $logger): Response
    {

        $this->setUserName();
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'username'=>$this->username
        ]);
    }

    /**
     * @Route("/admin/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->setUserName();
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword($user,$user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'username'=>$this->username
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        $this->setUserName();
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'username'=>$this->username
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->setUserName();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user,$user->getPassword()));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'username'=>$this->username
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
