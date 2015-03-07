<?php

namespace Worm\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Worm\SiteBundle\Entity\Worm;
use Worm\SiteBundle\Form\Type\WormType;

class WormController extends Controller
{

    /**
     * @return object
     */
    protected function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @return mixed
     */
    protected function getRepository()
    {
        return $this
            ->getEntityManager()
            ->getRepository('WormSiteBundle:Worm');
    }

    /**
     * @param Worm $worm
     * @return \Symfony\Component\Form\Form
     */
    protected function getForm(Worm $worm = null)
    {
        return $this->createForm(
            new WormType(),
            $worm
        );
    }

    /**
     *
     */
    public function listAction()
    {

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function viewAction(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Worm identifier not provided');
        }

        $worm = $this->getRepository()->find($id);

        if (!$worm) {
            throw $this->createNotFoundException('Worm identifier (' . $id . ') is invalid');
        }

        return $this->render(
            'WormSiteBundle:Worm:view.html.twig',
            array(
                'worm' => $worm
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction()
    {
        return $this->render(
            'WormSiteBundle:Worm:form.html.twig',
            array(
                'form' => $this->getForm(Worm::createDefault())->createView(),
                'worm' => null
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getEntityManager();

        $worm = new Worm();
        $worm->setAuthor($this->getUser());

        $form = $this->getForm($worm);

        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new Exception('Données invalides. Vérifier votre saisie');
            }

            $em->persist($worm);
            $em->flush();

            $worm->getQueue()->subscribe($this->getUser());
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Vers créé avec succès');

            return $this->redirect($this->generateUrl('wormsite_worm_view', array('id' => $worm->getId())));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->render(
            'WormSiteBundle:Worm:form.html.twig',
            array(
                'form' => $form->createView(),
                'worm' => null
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function editAction(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Worm identifier not provided');
        }

        $worm = $this->getRepository()->find($id);

        if (!$worm) {
            throw $this->createNotFoundException('Worm identifier (' . $id . ') is invalid');
        }

        return $this->render(
            'WormSiteBundle:Worm:form.html.twig',
            array(
                'form' => $this->getForm($worm)->createView(),
                'worm' => $worm
            )
        );
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function updateAction(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Worm identifier not provided');
        }

        $worm = $this->getRepository()->find($id);

        if (!$worm) {
            throw $this->createNotFoundException('Worm identifier (' . $id . ') is invalid');
        }

        $form = $this->getForm($worm);

        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new Exception('Données invalides. Vérifier votre saisie');
            }

            $this->getEntityManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Vers modifié avec succès');

            return $this->redirect($this->generateUrl('wormsite_worm_view', array('id' => $worm->getId())));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->render(
            'WormSiteBundle:Worm:form.html.twig',
            array(
                'form' => $form->createView(),
                'worm' => $worm
            )
        );
    }

}