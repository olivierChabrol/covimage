<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\ImageUnit;
use App\Entity\ImageStack;
use App\Form\ImageStackType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Psr\Log\LoggerInterface;

class MainController extends AbstractController
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
    public function visualize(string $token='',LoggerInterface $logger) {
        $this->setUserName();
        if (strlen($token)==14) {
            $ImageS = $this->getDoctrine()
                ->getRepository(ImageStack::class)
                ->findOneBy(['token' => $token]);
            if (!$ImageS) {
                return new Response("Pas trouvé !");
            }
            if ($ImageS->getAnalysed()) {
                return $this->render('visualize.html.twig', ['username'=>$this->username,'Stack'=>$ImageS,'analysed'=>true]);
            }
            return $this->render('visualize.html.twig', ['username'=>$this->username,'Stack'=>$ImageS, 'analysed'=>false]);
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
    public function image_stack(Request $request) {

        $this->setUserName();

        $ImageS = new ImageStack();

        $form = $this->createForm(ImageStackType::class,$ImageS);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ImageS->setDate(new \DateTimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            do {
                $token = bin2hex(random_bytes(7));
            } while ($this->getDoctrine()->getRepository(ImageStack::class)->findOneBy(['token' => $token]) !==null);
            $ImageS->setToken($token);
            $images = $ImageS->getUploadedFiles();
            $ImageS->setQuantity(count($images));
            $ImageS->setAnalysed(false);
            $ImageS->setUser($this->username);
            $i = 0;
            foreach ($images as $key => $image) {
                $imageUnit = new ImageUnit();
                $imageUnit->setSize($image->getSize());
                $imageUnit->setName(strval($i));
                $imageUnit->setImageFile($image);
                $ImageS->addImage($imageUnit);
                $entityManager->persist($imageUnit);
            }
            $entityManager->persist($ImageS);
            $entityManager->flush();
            return $this->redirectToRoute('uploaded',['token'=>$ImageS->getToken()]);
        } else {
            return $this->render('file_form.html.twig', ['username'=>$this->username,'form'=> $form->createView()]);
        }
    }
    public function uploaded(string $token) {
        if (strlen($token)==14) {
            $ImageS = $this->getDoctrine()
                ->getRepository(ImageStack::class)
                ->findOneBy(['token' => $token]);
            if (!$ImageS) {
                return new Response("Pas trouvé !");
            }
            return $this->render('uploaded.html.twig', ['username'=>$this->username,'Stack'=>$ImageS]);
        }
        return new Response("ID nul");
    }
    public function ajax_start_processing(Request $req) {
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
            $command = escapeshellcmd('../dicom.py '.$ImageS->getName().'-'.$token);
            exec($command,$output,$code);
            if ($code==0) {
                $ImageS->setAnalysed(true);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ImageS);
                $entityManager->flush();
                return new JsonResponse(array('success'=>true));
            } else {
                return new JsonResponse(array('success'=>false));
            }
        } else {
            return new Response("This url is for ajax only");
        }
    }
}
?>