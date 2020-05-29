<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\ImageStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends AbstractController
{
    public function welcome()
    {   
        return $this->render('home.html.twig');
    }
    public function show(string $token='')
    {   
        if (strlen($token)>0) {
            $ImageS = $this->getDoctrine()
                ->getRepository(ImageStack::class)
                ->findOneBy(['token' => $token]);
            if (!$ImageS) {
                return new Response("Pas trouvé !");
            }
            return $this->render('uploaded.html.twig', ['Stack'=>$ImageS]);
        }
        return new Response("ID nul");
    }
    public function visualize(string $token='') {
        if (strlen($token)>0) {
            $ImageS = $this->getDoctrine()
                ->getRepository(ImageStack::class)
                ->findOneBy(['token' => $token]);
            if (!$ImageS) {
                return new Response("Pas trouvé !");
            }
            if ($ImageS->getAnalysed()) {
                return $this->render('visualize.html.twig', ['Stack'=>$ImageS,'analysed'=>true]);
            }
            return $this->render('visualize.html.twig', ['Stack'=>$ImageS, 'analysed'=>false]);
        }
        return new Response("ID nul");
    }
    public function ajax_check_state(Request $req) {
        if ($req->isXMLHttpRequest()) {
            $token = $req->get('token');
            /**
             * @var ImageStack
             */
            $ImageS = $this->getDoctrine()->getRepository(ImageStack::class)->findOneBy(['token' => $token]);
            if ($ImageS===null) {
                return new Response("Invalid token");
            }
            if ($ImageS->getAnalysed()) {
                return new JsonResponse(array('success'=>true));
            } 
            return new JsonResponse(array('success'=>false));
        } else {
            return new Response("This url is for ajax only");
        }
    }
}
?>