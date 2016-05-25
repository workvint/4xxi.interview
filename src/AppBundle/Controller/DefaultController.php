<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{ 
    /**
     * @Route("/", name="index")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('portfolio_index');
    }
}
