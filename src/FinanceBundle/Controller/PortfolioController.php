<?php

namespace FinanceBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FinanceBundle\Entity\Portfolio;
use DirkOlbrich\YahooFinanceQuery\YahooFinanceQuery;

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

        $portfolios = $em->getRepository('FinanceBundle:Portfolio')
            ->findByUser($this->getUser());

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

            $this->addFlash('success', 'portfolio.new.message.success');
            
            return $this->redirectToRoute('portfolio_show', array(
                'id' => $portfolio->getId()
            ));
        }

        return $this->render('FinanceBundle:portfolio:new.html.twig', array(
            'portfolio' => $portfolio,
            'form'      => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Portfolio entity.
     *
     * @Route("/{id}", name="portfolio_show")
     * @Method("GET")
     * @Security("is_granted('show', portfolio)")
     */
    public function showAction(Portfolio $portfolio)
    {
        $deleteForm = $this->createDeleteForm($portfolio);

        return $this->render('FinanceBundle:portfolio:show.html.twig', array(
            'portfolio'     => $portfolio,
            'profit_graph'  => array_values($this->profitGraph($portfolio)),
        ));
    }

    /**
     * Displays a form to edit an existing Portfolio entity.
     *
     * @Route("/{id}/edit", name="portfolio_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('edit', portfolio)")
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

            $this->addFlash('success', 'portfolio.edit.message.success');
                
            return $this->redirectToRoute('portfolio_edit', array(
                'id' => $portfolio->getId()
            ));
        }

        return $this->render('FinanceBundle:portfolio:edit.html.twig', array(
            'portfolio'     => $portfolio,
            'edit_form'     => $editForm->createView(),
            'delete_form'   => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Portfolio entity.
     *
     * @Route("/{id}", name="portfolio_delete")
     * @Method("DELETE")
     * @Security("is_granted('delete', portfolio)")
     */
    public function deleteAction(Request $request, Portfolio $portfolio)
    {
        $form = $this->createDeleteForm($portfolio);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($portfolio);
            $em->flush();
            
            $this->addFlash('success', 'portfolio.delete.message.success');
        }

        return $this->redirectToRoute('portfolio_index');
    }

    /**
     * Creates a form to delete a Portfolio entity.
     *
     * @param Portfolio $portfolio The Portfolio entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Portfolio $portfolio)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('portfolio_delete', array(
                'id' => $portfolio->getId()
            )))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Portfolio profit graph
     * 
     * @todo cache it, if portfolio didn't edited 
     * 
     * @param Portfolio $portfolio The Portfolio entity
     * @return array
     */
    private function profitGraph(Portfolio $portfolio, $date='2 year')
    {
        $graph = array();
        
        $startDate  = date('Y-m-d', strtotime('-' . $date));
        if (false === $startDate) {
            return $graph;
        }
        
        $query = new YahooFinanceQuery();
        // weekly 
        $param      = 'w';
        $fieldPrice = 'AdjClose';
        $fieldDate  = 'Date';
        
        foreach ($portfolio->getItems() as $portfolioItem) {
            
            $queryResult = $query->historicalQuote($portfolioItem->getStock()->getCode(),
                $startDate, '', $param
            )->get();
            
            $count = count($queryResult);
            if ($count) {
                for ($i = $count - 1; $i--; $i >= 0) {
                    $item = $queryResult[$i];
                
                    $point = array(
                        $item[$fieldDate],
                        $item[$fieldPrice] * $portfolioItem->getAmount(),
                    );

                    if (key_exists($item[$fieldDate], $graph)) {
                        $point[1] += $graph[$item[$fieldDate]][1];
                    }

                    $graph[$item[$fieldDate]] = $point;
                }
            }
        }
        
        return $graph;
    }
}
