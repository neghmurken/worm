<?php

namespace Worm\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubmissionController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function wormAction(Request $request)
    {
        $submissions = $this
            ->get('doctrine.orm.entity_manager')
            ->getRepository('WormSiteBundle:Submission')
            ->retrieveAll();

        return $this->render(
            'WormSiteBundle:Submission:worm.html.twig',
            array(
                'submissions' => $submissions,
                'queue'       => $this->get('worm_site.queue')
            )
        );
    }
}