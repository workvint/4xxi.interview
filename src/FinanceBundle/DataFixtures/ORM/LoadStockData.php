<?php

namespace FinanceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FinanceBundle\Entity\Stock;
use DirkOlbrich\YahooFinanceQuery\YahooFinanceQuery;

/**
 * 
 */
class LoadStockData implements FixtureInterface 
{
    public function load(ObjectManager $manager) 
    {
        $query = new YahooFinanceQuery();
        $queryResult = $query->indexList(array('^IXCO'))->get();
        
        foreach ($queryResult as $page) {
            /**
             * bug in Yahoo Finance package, only fist page (first 50 items)
             */
            foreach ($page['components'] as $item) {
                $stock = new Stock();
                
                $stock->setTitle($item['Name']);
                $stock->setCode($item['Symbol']);
                
                $manager->persist($stock);
            }
        }
        
        $manager->flush();
    }
}
