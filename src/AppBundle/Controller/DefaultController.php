<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{ 
    /**
     * @Route("/p", name="home")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        $user = $this->getUser();
        
        return new Response($user->getUsername());
    }
    
    /**
     * @Route("/po/{id}", requirements={"id": "\d+"}, name="portfolio")
     * @Security("has_role('ROLE_USER')")
     */
    public function portfolioAction()
    {
        return new Response('portfolioAction');
    }
}
