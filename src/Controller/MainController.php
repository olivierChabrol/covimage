<?php
// src/Controller/MainController.php
namespace App\Controller;

use App\Entity\ImageUnit;
use App\Entity\ImageStack;
use App\Form\ImageStackType;
use App\Entity\Params;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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

    public function download(string $token)
    {
        if (strlen($token)==14) {
            $ImageS = $this->getDoctrine()->getRepository(ImageStack::class)->findOneBy(['token' => $token]);

            $outputPath = $this->getDoctrine()->getRepository(Params::class)->findOneBy(['type_param'=>Params::SCRIPT_OUTPUT_TYPE])->getValue();
            $dir = $ImageS->getName().'-'.$token;
            // Create new Zip Archive.
            $zip = new \ZipArchive();

            // The name of the Zip documents.
            $zipName = 'Documents.zip';

            $zip->open($zipName,  \ZipArchive::CREATE);
            foreach ($ImageS->getImages() as $file) {
                $fileName = str_replace("dcm", "png", basename($file->getName()));
                $zip->addFromString($fileName,  file_get_contents($outputPath.$dir."/".$fileName));
            }
            $zip->addFromString(basename($outputPath.$dir."/t.txt"),  file_get_contents($outputPath.$dir."/t.txt"));
            $zip->close();

            $response = new Response(file_get_contents($zipName));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
            $response->headers->set('Content-length', filesize($zipName));

            @unlink($zipName);

            return $response;
        }
        return new Response("ID nul");
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
                $outputPath = $this->getDoctrine()->getRepository(Params::class)->findOneBy(['type_param'=>Params::SCRIPT_OUTPUT_TYPE])->getValue();
                $dir = $ImageS->getName().'-'.$token;
                $txt_file    = file_get_contents($outputPath.$dir."/t.txt");  
                return $this->render('visualize.html.twig', ['token'=>$token, 'username'=>$this->username,'Stack'=>$ImageS,'analysed'=>true, 'path'=> $outputPath.$dir,'txt'=>$txt_file]);
            }
            //$txt_file    = file_get_contents('path/to/file.txt');
            return $this->render('visualize.html.twig', ['username'=>$this->username,'Stack1'=>$ImageS, 'analysed'=>false, 'token'=>$token]);
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
            $commands = $this->getDoctrine()->getRepository(Params::class)->findBy(['type_param'=>Params::SCRIPT_PATH_TYPE]);
            return $this->render('uploaded.html.twig', ['username'=>$this->username,'Stack'=>$ImageS,'commands'=>$commands]);
        }
        return new Response("ID nul");
    }
    public function ajax_start_processing(Request $req, LoggerInterface $logger) {
        if ($req->isXMLHttpRequest()) {
            $token = $req->get('token');
            $paramId = $req->get('command');
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
            $repo =$this->getDoctrine()->getRepository(Params::class);
            //return new JsonResponse(array('success'=>true, 'Params'=>Params::SCRIPT_PATH_TYPE, 'data'=>$repo->findOneBy(['type_param'=>Params::SCRIPT_PATH_TYPE])->getValue()));
            $commandPath = $repo->findOneBy(['id'=>$paramId])->getValue();
            $outputPath = $repo->findOneBy(['type_param'=>Params::SCRIPT_OUTPUT_TYPE])->getValue();
            $dir = __DIR__;
            $logger->info("dir : ".$dir);
            $command = escapeshellcmd('../'.$commandPath.' '.$ImageS->getName().'-'.$token.' '.$outputPath);
            $logger->info("command : ".$command);
            //$output = array();
            $output = exec($command);
            $logger->info("output : ".$output);

            $process = new Process([$commandPath, $ImageS->getName().'-'.$token, $outputPath]);

            try {
                $process->mustRun();

                $logger->info("Process output : ".$process->getOutput());
                $ImageS->setAnalysed(true);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ImageS);
                $entityManager->flush();
                return new JsonResponse(array('success'=>true));
            } catch (ProcessFailedException $exception) {
                $logger->error("Exception message : ".$exception->getMessage());
                return new JsonResponse(array('success'=>false,'data'=>$exception->getMessage()));
            }
        } else {
            return new Response("This url is for ajax only");
        }
    }
}
?>