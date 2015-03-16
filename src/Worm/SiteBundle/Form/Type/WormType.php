<?php

namespace Worm\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Worm\SiteBundle\Entity\Worm;
use Worm\SiteBundle\Form\Transformer\TimeLimitTransformer;

class WormType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isNew = !$builder->getData()->getId();

        $builder->add('name', 'text', array(
            'label' => 'Title',
            'required' => false
        ));

        $builder->add('description', 'textarea', array(
            'label' => 'Description',
            'required' => false
        ));

        $builder->add('mode', 'choice', array(
            'label' => 'Orientation',
            'choices' => Worm::getModes(),
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'disabled' => !$isNew
        ));

        $builder->add('width', 'integer', array(
            'label' => 'Width',
            'required' => false,
            'disabled' => !$isNew && $builder->getData()->getMode() === Worm::MODE_VERTICAL
        ));

        $builder->add('height', 'integer', array(
            'label' => 'Height',
            'required' => false,
            'disabled' => !$isNew && $builder->getData()->getMode() === Worm::MODE_HORIZONTAL
        ));

        $builder->add('uniqueQueue', 'checkbox', array(
            'label' => 'Do not allow the same participant more than once in the queue',
            'required' => false
        ));

        $builder->add(
            $builder->create('timeLimit', 'number', array(
                'label' => 'Maximum time allowed',
                'required' => true
            ))
            ->addModelTransformer(new TimeLimitTransformer())
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Worm\\SiteBundle\\Entity\\Worm'
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'wormsite_worm';
    }

}