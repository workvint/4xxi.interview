<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Security("has_role('ROLE_USER')")
 */
class DefaultController extends Controller
{ 
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->redirectToRoute('portfolio_index');
    }
}
