<?php

namespace FinanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FinanceBundle\Form\PortfolioItemType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class PortfolioType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'portfolio.new.form.title.label'))
            ->add('items', CollectionType::class, array(
                'label'         => 'portfolio.new.form.items.label',
                'allow_add'     => true,
                'allow_delete'  => true,
                'prototype'     => true,
                'entry_type'    => PortfolioItemType::class,
                'entry_options' => array(
                    'label' => false, 
                    'attr'  => array('class' => 'form-inline')
                ),
            ))
            ->add('add', ButtonType::class, array(
                'label' => 'portfolio.new.form.items.button.add',    
                'attr'  => array('class' => 'dcollection_add_item'),
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
