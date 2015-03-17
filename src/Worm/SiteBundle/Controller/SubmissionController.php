<?php

namespace Worm\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{

    /**
     * @return object
     */
    protected function getEntityManager()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * @param Request $request
     * @return StreamedResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function downloadAction(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            throw $this->createNotFoundException('Submission identifier not provided');
        }

        $submission = $this
            ->getEntityManager()
            ->getRepository('WormSiteBundle:Submission')
            ->find($id);

        if (!$submission) {
            throw $this->createNotFoundException('Submission identifier (' . $id . ') is invalid');
        }

        $filename = $this
            ->get('worm_site.image_manager')
            ->getImagePath($submission);

        return new StreamedResponse(
            function () use ($filename) {
                readfile($filename);
            },
            200,
            array(
                'Content-Type' => $submission->getMimeType(),
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => 'attachment;filename=' . basename($filename)
            )
        );
    }
}