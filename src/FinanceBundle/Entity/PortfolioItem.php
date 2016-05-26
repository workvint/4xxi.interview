<?php

namespace FinanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PortfolioItem
 *
 * @ORM\Table(name="portfolio_item")
 * @ORM\Entity(repositoryClass="FinanceBundle\Repository\PortfolioItemRepository")
 */
class PortfolioItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Stock
     * 
     * @ORM\ManyToOne(targetEntity="Stock")
     * @Assert\NotNull
     */
    private $stock;

    /**
     * @var Portfolio
     * 
     * @ORM\ManyToOne(targetEntity="Portfolio", inversedBy="items")
     */
    private $portfolio;
    
    /**
     * @var int
     *
     * @ORM\Column(name="amount", type="integer")
     * @Assert\Range(min=1, max=1000)
     */
    private $amount;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return PortfolioItem
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set stock
     *
     * @param \FinanceBundle\Entity\Stock $stock
     * @return PortfolioItem
     */
    public function setStock(\FinanceBundle\Entity\Stock $stock)
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * Get stock
     *
     * @return \FinanceBundle\Entity\Stock 
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set portfolio
     *
     * @param \FinanceBundle\Entity\Portfolio $portfolio
     * @return PortfolioItem
     */
    public function setPortfolio(\FinanceBundle\Entity\Portfolio $portfolio)
    {
        $this->portfolio = $portfolio;

        return $this;
    }

    /**
     * Get portfolio
     *
     * @return \FinanceBundle\Entity\Portfolio 
     */
    public function getPortfolio()
    {
        return $this->portfolio;
    }
}
