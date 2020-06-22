<?php

namespace App\Controller;

use App\Entity\Params;
use App\Form\ParamsType;
use App\Repository\ParamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/params")
 */
class ParamsController extends AbstractController
{
    /**
     * @Route("/", name="params_index", methods={"GET"})
     */
    public function index(ParamsRepository $paramsRepository): Response
    {
        return $this->render('params/index.html.twig', [
            'params' => $paramsRepository->findAll(),
            //'params' => $paramsRepository->findBy(['type_param' => Params::PARAM]),
        ]);
    }

    /**
     * @Route("/new", name="params_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityGet = $this->getDoctrine()->getRepository(Params::Class);
        $param = new Params();
        $form = $this->createForm(ParamsType::class, $param, ['entity_manager' => $entityManager,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $entityGet->findOneBy(['type_param'=> $param->getTypeParam() , 'value'=> $param->getValue()]) == NULL) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($param);
            $entityManager->flush();

            return $this->redirectToRoute('params_index');
        }

        return $this->render('params/new.html.twig', [
            'param' => $param,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="params_show", methods={"GET"})
     */
    public function show(Params $param): Response
    {
        return $this->render('params/show.html.twig', [
            'param' => $param,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="params_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Params $param): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ParamsType::class, $param, ['entity_manager' => $entityManager,]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $param->getId() != 1) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('params_index');
        }

        return $this->render('params/edit.html.twig', [
            'param' => $param,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="params_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Params $param): Response
    {
        if ($this->isCsrfTokenValid('delete'.$param->getId(), $request->request->get('_token')) && $param->getId() != 1) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($param);
            $entityManager->flush();
        }

        return $this->redirectToRoute('params_index');
    }
}
