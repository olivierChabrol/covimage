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
            $username = "inconnu";
        } else {
            $username = $user->getFirstName();
        }
        
        return $this->render('home/username.html.twig', [
            'username' => $username,
        ]);
    }
}
