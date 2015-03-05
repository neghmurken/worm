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
        $builder->add('name', 'text', array(
            'label' => 'Titre',
            'required' => false
        ));

        $builder->add('mode', 'choice', array(
            'label' => 'Orientation',
            'choices' => Worm::getModes(),
            'expanded' => true,
            'multiple' => false,
            'required' => true
        ));

        $builder->add('width', 'integer', array(
            'label' => 'Largeur',
            'required' => false
        ));

        $builder->add('height', 'integer', array(
            'label' => 'Hauteur',
            'required' => false
        ));

        $builder->add('uniqueQueue', 'checkbox', array(
            'label' => 'Ne pas autoriser plus d\'une fois le même participant dans la file d\'attente',
            'required' => false
        ));

        $builder->add(
            $builder->create('timeLimit', 'number', array(
                'label' => 'Temps imparti maximum',
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