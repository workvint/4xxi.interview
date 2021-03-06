<?php

namespace FinanceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class PortfolioItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stock', null, array('label' => 'portfolio.new.form.items.title.label'))
            ->add('amount', null, array('label' => 'portfolio.new.form.items.amount.label'))
            ->add('remove', ButtonType::class, array(
                'label' => 'portfolio.new.form.items.button.remove',
                'attr'  => array('class' => 'dcollection_remove_item'),
            ))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FinanceBundle\Entity\PortfolioItem'
        ));
    }
}
