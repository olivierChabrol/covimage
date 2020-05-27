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

class FormController extends AbstractController
{
    public function new(Request $request) {
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
            return $this->render('file_form.html.twig', ['form'=> $form->createView()]);
        }
    }

    public function image_stack(Request $request) {
        $ImageS = new ImageStack();

        $form = $this->createForm(ImageStackType::class,$ImageS);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ImageS->setDate(new \DateTimeImmutable());
            $entityManager = $this->getDoctrine()->getManager();
            $images = $ImageS->getUploadedFiles();
            $ImageS->setQuantity(count($images));
            foreach ($images as $key => $image) {
                $imageUnit = new ImageUnit();
                $imageUnit->setSize($image->getSize());
                $imageUnit->setName($image->getClientOriginalName());
                $imageUnit->setImageFile($image);
                $ImageS->addImage($imageUnit);
                $entityManager->persist($imageUnit);
            }
            $entityManager->persist($ImageS);
            $entityManager->flush();
            echo "Stack créée";
            return $this->render('uploaded.html.twig', ['Images'=>$ImageS->getImages()]);
        } else {
            return $this->render('file_form.html.twig', ['form'=> $form->createView()]);
        }
    }
}
?>