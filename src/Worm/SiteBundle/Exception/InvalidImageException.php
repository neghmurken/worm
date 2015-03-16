<?php

namespace Worm\SiteBundle\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class InvalidImageException extends \Exception
{
    protected $violations;

    /**
     * @param string $message
     * @param ConstraintViolationListInterface $violations
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(
        $message,
        ConstraintViolationListInterface $violations = null,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->violations = $violations;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }

}