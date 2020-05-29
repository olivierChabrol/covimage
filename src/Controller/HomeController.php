<?php
// src/Controller/HomeController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

use Psr\Log\LoggerInterface;

class HomeController extends AbstractController
{
    public function homepage(LoggerInterface $logger)
    {
        $user = $this->getUser();
        if (!$user) {
            $username = "anonymous";
        } else {
            $username = $user->getEmail();
        }
        
        return $this->render('home.html.twig', [
            'username' => $username,
        ]);
    }
}
