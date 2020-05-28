<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

    public function add_user()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        
        return $this->render('security/add_user.html.twig', [
    
        ]);
    }


    public function delete_user()
    {
        //throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
        
        return $this->render('security/delete_user.html.twig', [
    
        ]);
    }

    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
