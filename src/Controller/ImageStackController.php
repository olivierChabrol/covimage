<?php

namespace App\Controller;

use App\Entity\ImageStack;
use App\Form\ImageStackType;
use App\Repository\ImageStackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

use Psr\Log\LoggerInterface;
/**
 * @Route("/admin/analysis")
 */
class ImageStackController extends AbstractController
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
    /**
     * @Route("/", name="image_stack_index", methods={"GET"})
     */
    public function index(ImageStackRepository $imageStackRepository,LoggerInterface $logger): Response
    {
        return $this->render('image_stack/index.html.twig', [
            'image_stacks' => $imageStackRepository->findAll(),
            'username'=> $this->username,
        ]);
    }

    /**
     * @Route("/new", name="image_stack_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        return $this->redirectToRoute('analyse');
    }

    /**
     * @Route("/{id}", name="image_stack_show", methods={"GET"})
     */
    public function show(ImageStack $imageStack): Response
    {
        return $this->render('image_stack/show.html.twig', [
            'image_stack' => $imageStack,
            'username'=> $this->username,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="image_stack_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ImageStack $imageStack): Response
    {
        $form = $this->createForm(ImageStackType::class, $imageStack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_stack_index');
        }
        return $this->render('image_stack/edit.html.twig', [
            'image_stack' => $imageStack,
            'username'=> $this->username,
            'form'=>$form->createView(),
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
                $filesys->remove(str_replace('results','uploads',$dir));
            }
            $entityManager->remove($imageStack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_stack_index');
    }
}
