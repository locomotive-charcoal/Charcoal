<?php

namespace Charcoal\Property;

// From 'charcoal-property'
use Charcoal\Property\StringProperty;

/**
 * Email Property. Email address.
 */
class EmailProperty extends StringProperty
{
    /**
     * @return string
     */
    public function type()
    {
        return 'email';
    }

    /**
     * Email's maximum length is defined in RFC-3696 (+ errata) as 254 characters.
     *
     * This overrides PropertyString's maxLength() to ensure compliance with the email standards.
     *
     * @return integer
     */
    public function getMaxLength()
    {
        return 254;
    }

    /**
     * @return array
     */
    public function validationMethods()
    {
        $parentMethods = parent::validationMethods();

        return array_merge($parentMethods, [
            'email',
        ]);
    }

    /**
     * @return boolean
     */
    public function validateEmail()
    {
        $val = $this->val();
        $emailValid = filter_var($val, FILTER_VALIDATE_EMAIL);
        return !!$emailValid;
    }

    /**
     * @param mixed $val A single value to parse.
     * @see AbstractProperty::parseOne()
     * @see AbstractProperty::parseVal()
     * @return string
     */
    public function parseOne($val)
    {
        return filter_var(strip_tags($val), FILTER_SANITIZE_EMAIL);
    }
}
