<?php

namespace Creavo\MultiAppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CreavoMultiAppBundle:Default:index.html.twig');
    }
}
