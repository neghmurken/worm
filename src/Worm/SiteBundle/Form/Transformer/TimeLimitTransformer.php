<?php

namespace Worm\SiteBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class TimeLimitTransformer implements DataTransformerInterface
{

    /**
     * @param mixed $value
     * @return float|mixed
     */
    public function transform($value)
    {
        return $value / (60 * 24);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function reverseTransform($value)
    {
        return (int)$value * 60 * 24;
    }
}