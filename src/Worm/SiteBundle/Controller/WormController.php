<?php

namespace Worm\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Worm\SiteBundle\Entity\Worm;
use Worm\SiteBundle\Exception\InvalidImageException;
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
     * @param Request $request
     * @return Worm
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getWorm(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Worm identifier not provided');
        }

        $worm = $this->getRepository()->find($id);

        if (!$worm) {
            throw $this->createNotFoundException('Worm identifier (' . $id . ') is invalid');
        }

        return $worm;
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
        return $this->render(
            'WormSiteBundle:Worm:view.html.twig',
            array(
                'worm' => $this->getWorm($request),
                'im' => $this->get('worm_site.image_manager')
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
                throw new Exception('Invalid data. Check your input');
            }

            $em->persist($worm);
            $em->flush();

            $worm->getQueue()->subscribe($this->getUser());
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Worm successfully created');

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
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function editAction(Request $request)
    {
        $worm = $this->getWorm($request);

        if ($worm->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedHttpException();
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
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function updateAction(Request $request)
    {
        $worm = $this->getWorm($request);
        $form = $this->getForm($worm);

        if ($worm->getAuthor()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedHttpException();
        }

        try {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                throw new Exception('Invalid data. Check your input');
            }

            $this->getEntityManager()->flush();

            $this->get('session')->getFlashBag()->add('success', 'Worm successfully updated');

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

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function submitAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();

        $worm = $this->getWorm($request);
        $queue = $worm->getQueue();

        if ($queue->getCurrent() && $queue->getCurrent()->getUser()->getId() !== $this->getUser()->getId()) {
            throw new AccessDeniedHttpException('It\'s not your turn');
        }

        $im = $this->get('worm_site.image_manager');
        $em = $this->getEntityManager();

        try {

            $submission = $queue->next();
            $im->register($request->files->get('image'), $submission);

            $em->persist($submission);
            $em->flush();

            $flashbag->add('success', 'Submission accepted!');
        } catch (InvalidImageException $e) {
            $flashbag->add('error', 'Invalid image'); // TODO append all errors from violations list
        } catch (\Exception $e) {
            $flashbag->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('wormsite_worm_view', array('id' => $worm->getId())));
    }

}