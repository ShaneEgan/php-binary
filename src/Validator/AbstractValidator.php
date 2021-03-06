<?php
/**
 * php-binary
 * A PHP library for parsing structured binary streams
 *
 * @package  php-binary
 * @author Damien Walsh <me@damow.net>
 */
namespace Binary\Validator;

/**
 * AbstractValidator
 *
 * @since 1.0
 */
abstract class AbstractValidator implements ValidatorInterface
{
    protected $desiredValue;

    /**
     * Initialise a new validator.
     *
     * @param mixed $desiredValue
     */
    public function __construct($desiredValue = null)
    {
        $this->desiredValue = $desiredValue;
    }

    /**
     * @param mixed $desiredValue
     */
    public function setDesiredValue($desiredValue)
    {
        $this->desiredValue = $desiredValue;
    }

    /**
     * @return mixed
     */
    public function getDesiredValue()
    {
        return $this->desiredValue;
    }
}
