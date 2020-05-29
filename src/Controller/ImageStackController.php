<?php

namespace App\Controller;

use App\Entity\ImageStack;
use App\Form\ImageStack1Type;
use App\Repository\ImageStackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route("/image/stack")
 */
class ImageStackController extends AbstractController
{
    /**
     * @Route("/", name="image_stack_index", methods={"GET"})
     */
    public function index(ImageStackRepository $imageStackRepository): Response
    {
        return $this->render('image_stack/index.html.twig', [
            'image_stacks' => $imageStackRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="image_stack_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $imageStack = new ImageStack();
        $form = $this->createForm(ImageStack1Type::class, $imageStack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($imageStack);
            $entityManager->flush();

            return $this->redirectToRoute('image_stack_index');
        }

        return $this->render('image_stack/new.html.twig', [
            'image_stack' => $imageStack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_stack_show", methods={"GET"})
     */
    public function show(ImageStack $imageStack): Response
    {
        return $this->render('image_stack/show.html.twig', [
            'image_stack' => $imageStack,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="image_stack_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ImageStack $imageStack): Response
    {
        $form = $this->createForm(ImageStack1Type::class, $imageStack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_stack_index');
        }

        return $this->render('image_stack/edit.html.twig', [
            'image_stack' => $imageStack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="image_stack_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ImageStack $imageStack): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imageStack->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($imageStack->getAnalysed()) {
                $filesys = new Filesystem();
                $dir = "images/results/".strtolower($imageStack->getName()).'-'.$imageStack->getToken();
                foreach ($imageStack->getImages() as $unit) {
                    try {
                        $filesys->remove($dir."/".$unit->getName());
                    } catch (IOExceptionInterface $exception) {
                        echo "An error occurred while creating your directory at ".$exception->getPath();
                    }
                }
                $filesys->remove($dir);
            }
            $entityManager->remove($imageStack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_stack_index');
    }
}
