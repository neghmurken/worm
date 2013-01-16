<?php

namespace Worm\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{

    public function indexAction(Request $request)
    {

        return $this->render(
            'WormSiteBundle:Home:index.html.twig'
        );
    }
}