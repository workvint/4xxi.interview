<?php

namespace FinanceBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use UserBundle\Entity\User;
use FinanceBundle\Entity\Portfolio;

/**
 * 
 */
class PortfolioVoter extends Voter 
{   
    const attributes = array(
        'show', 'edit', 'delete',
    );
    
    protected function supports($attribute, $subject) 
    {
        if (!in_array($attribute, self::attributes)) {
            return false;
        }
        
        if (!$subject instanceof Portfolio) {
            return false;
        }
        
        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) 
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        
        return $subject->getUser() === $user;
    }

}
