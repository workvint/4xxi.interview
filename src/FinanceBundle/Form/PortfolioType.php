<?php

namespace FinanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FinanceBundle\Form\PortfolioItemType;

class PortfolioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('user')
            ->add('items', CollectionType::class, array(
                'label'         => 'Акции',
                'entry_type'    => PortfolioItemType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'prototype'     => true
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FinanceBundle\Entity\Portfolio'
        ));
    }
}
