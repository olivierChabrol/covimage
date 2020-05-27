<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\ImageStack;
use App\Form\ImageStackType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function index()
    {
        return $this->render('home.html.twig');
    }
    public function show(int $id=0)
    {   
        if ($id>0) {
            $ImageS = $this->getDoctrine()
                ->getRepository(ImageStack::class)
                ->find($id);
            if (!$ImageS) {
                return new Response("Pas trouvé !");
            }
            return $this->render('uploaded.html.twig', ['Images'=>$ImageS->getImages()]);
        }
        return new Response("ID nul");
    }
}
?>