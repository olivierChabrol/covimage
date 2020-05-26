<?php
// src/Controller/MainController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig');
    }
    public function upload()
    {
        return $this->render('upload.html.twig');
    }
    public function files(Request $req)
    {
        $requestStr = var_dump($req);
        return new Response("Uploadé");
    }
}
?>