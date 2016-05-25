<?php

namespace FinanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FinanceBundle\Entity\Portfolio;

/**
 * Portfolio controller.
 *
 * @Route("/portfolio")
 * @Security("has_role('ROLE_USER')")
 */
class PortfolioController extends Controller
{
    /**
     * Lists all Portfolio entities.
     *
     * @Route("/", name="portfolio_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $portfolios = $em->getRepository('FinanceBundle:Portfolio')->findAll();

        return $this->render('FinanceBundle:portfolio:index.html.twig', array(
            'portfolios' => $portfolios,
        ));
    }

    /**
     * Creates a new Portfolio entity.
     *
     * @Route("/new", name="portfolio_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $portfolio = new Portfolio();
        $form = $this->createForm('FinanceBundle\Form\PortfolioType', $portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $portfolio->setUser($this->getUser());
            
            foreach ($portfolio->getItems() as $portfolioItem) {
                $portfolioItem->setPortfolio($portfolio);
            }
            
            $em->persist($portfolio);
            $em->flush();

            return $this->redirectToRoute('portfolio_show', array('id' => $portfolio->getId()));
        }

        return $this->render('FinanceBundle:portfolio:new.html.twig', array(
            'portfolio' => $portfolio,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Portfolio entity.
     *
     * @Route("/{id}", name="portfolio_show")
     * @Method("GET")
     */
    public function showAction(Portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);

        return $this->render('FinanceBundle:portfolio:show.html.twig', array(
            'portfolio' => $portfolio,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Portfolio entity.
     *
     * @Route("/{id}/edit", name="portfolio_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);
        $editForm = $this->createForm('FinanceBundle\Form\PortfolioType', $portfolio);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $existedItems = $em->getRepository('FinanceBundle:PortfolioItem')
                ->findByPortfolio($portfolio);
            $formItems = array();
            foreach ($portfolio->getItems() as $portfolioItem) {
                if ($portfolioItem->getId()) {
                    $formItems[$portfolioItem->getId()] = $portfolioItem;
                } else {
                    $portfolioItem->setPortfolio($portfolio);
                }
            }
            
            foreach ($existedItems as $existedItem) {
                if (!key_exists($existedItem->getId(), $formItems)) {
                    $em->remove($existedItem);
                }
            }
            
            $em->persist($portfolio);
            $em->flush();

            return $this->redirectToRoute('portfolio_edit', array('id' => $portfolio->getId()));
        }

        return $this->render('FinanceBundle:portfolio:edit.html.twig', array(
            'portfolio' => $portfolio,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Portfolio entity.
     *
     * @Route("/{id}", name="portfolio_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Portfolio $portfolio)
    {
        $form = $this->createDeleteForm($portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($portfolio);
            $em->flush();
        }

        return $this->redirectToRoute('portfolio_index');
    }

    /**
     * Creates a form to delete a Portfolio entity.
     *
     * @param Portfolio $portfolio The Portfolio entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Portfolio $portfolio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', array('id' => $portfolio->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
