<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{

    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger): Response
    {
        //$logger->error("[SecurityController] [login]erreur connexion produite");
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $logger->info("[SecurityController][login] $error");
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }
    
    public function profil()
    {       
        $user = $this->getUser();
        return $this->render('security/profil.html.twig', [
            'user'=>$user,
        ]);
    }

    public function update_password_profil(Request $req, UserPasswordEncoderInterface $passwordEncoder) 
    {
        if ($req->isXMLHttpRequest()) {
            $id = $req->get('user');
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->find($id);
            $user->setPassword($passwordEncoder->encodePassword($user,$req->get('password')));
            if (!$user) 
            {
                throw $this->createNotFoundException(
                    'No user found for id ' . $user
                );
            }
            $em->flush();
            return new JsonResponse(array('success'=>true));
            //return $this->redirectToRoute('profil');
        } else {
            return new Response("This url is for ajax only");
        }
}
}
