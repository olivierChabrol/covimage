<?php
// src/Controller/FormController.php
namespace App\Controller;

use App\Entity\Fichier;
use App\Entity\ImageStack;
use App\Entity\ImageUnit;
use App\Form\ImageStackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Form\Type\VichFileType;

use Psr\Log\LoggerInterface;

class FormController extends AbstractController
{
    public function new(Request $request, LoggerInterface $logger) {

        $user = $this->getUser();
        if (!$user) {
            $username = "anonymous";
        } else {
            $username = $user->getEmail();
        }

        $fichier = new Fichier();

        $form = $this->createFormBuilder($fichier)
            ->add('imageFile', VichFileType::class, [
                'required' => true,
                'label' => 'Choississez vos fichiers :'
                ])
            ->add('save', SubmitType::class, ['label'=> 'Envoyer'])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fichier = $form->getData();
            $fichier->setUpdatedAt(new \DateTimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fichier);
            $entityManager->flush();
            return $this->render('uploaded.html.twig', ['Images'=>array($fichier)]);
        } else {
            return $this->render('file_form.html.twig', ['username'=>$username,'form'=> $form->createView()]);
        }
    }

    public function image_stack(Request $request, LoggerInterface $logger) {

        $user = $this->getUser();
        if (!$user) {
            $username = "anonymous";
        } else {
            $username = $user->getEmail();
        }

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
            return $this->redirectToRoute('visualize',['token'=>$ImageS->getToken()]);
        } else {
            return $this->render('file_form.html.twig', ['username'=>$username,'form'=> $form->createView()]);
        }
    }
}
?>