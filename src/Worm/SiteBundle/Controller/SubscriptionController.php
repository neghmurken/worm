<?php

namespace Worm\SiteBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Worm\SiteBundle\Exception\InvalidImageException;

class SubscriptionController extends Controller
{

    /**
     * @return ObjectManager
     */
    protected function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Request $request
     * @return object
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function getSubscription(Request $request)
    {
        $id = $request->get('id');
        $position = $request->get('pos');

        if (!$id) {
            throw $this->createNotFoundException('Subscription identifier not provided');
        }

        $subscription = $this
            ->getEntityManager()
            ->getRepository('WormSiteBundle:Subscription')
            ->findOne($id, $position);

        if (!$subscription) {
            throw $this->createNotFoundException('Subscription identifier (' . $id . ') is invalid');
        }

        return $subscription;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws AccessDeniedHttpException
     */
    public function submitAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();

        $subscription = $this->getSubscription($request);
        $worm = $subscription->getWorm();

        if (!$this->get('security.context')->isGranted('SUBSCRIPTION_SUBMIT', $subscription)) {
            throw new AccessDeniedHttpException;
        }

        $queue = $worm->getQueue();

        $im = $this->get('worm_site.image_manager');
        $em = $this->getEntityManager();

        try {
            $submission = $queue->next();
            $im->register($request->files->get('image'), $submission);

            $em->persist($submission);
            $em->flush();

            $flashbag->add('success', 'Submission accepted!');
        } catch (InvalidImageException $e) {
            $flashbag->add(
                'error',
                'Invalid image. ' . $e->getMessage()
            ); // TODO append all errors from violations list
        } catch (\Exception $e) {
            $flashbag->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('wormsite_worm_view', array('id' => $worm->getId())));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function withdrawAction(Request $request)
    {
        $flashbag = $this->get('session')->getFlashBag();

        $subscription = $this->getSubscription($request);
        $worm = $subscription->getWorm();

        $em = $this->getEntityManager();

        if (!$this->get('security.context')->isGranted('SUBSCRIPTION_WITHDRAW', $subscription)) {
            throw new AccessDeniedHttpException;
        }

        try {
            $worm->getQueue()->withdraw($subscription->getPosition());
            $em->flush();

            $flashbag->add('success', 'Submission withdrawn');
        } catch (\Exception $e) {
            $flashbag->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('wormsite_worm_view', array('id' => $worm->getId())));
    }

}