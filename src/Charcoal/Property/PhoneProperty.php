<?php

namespace Charcoal\Property;

// From 'charcoal-property'
use Charcoal\Property\StringProperty;

/**
 * Telephone Property
 *
 * Phone numbers.
 */
class PhoneProperty extends StringProperty
{
    /**
     * @return string
     */
    public function type()
    {
        return 'phone';
    }

    /**
     * Set StringProperty's `defaultMaxLength` to 16 for phone numbers.
     *
     * @return integer
     */
    public function defaultMaxLength()
    {
        return 16;
    }

    /**
     * Sanitize a phone value by removing all non-digit characters.
     *
     * @param mixed $val Optional. The value to sanitize. If none provided, use `val()`.
     * @return string
     */
    public function sanitize($val)
    {
        return preg_replace('/[^0-9]/', '', $val);
    }

    /**
     * @param string $val Optional. The value to display. If none is provided, use `val()`.
     * @return string
     */
    public function displayVal($val)
    {
        $val = $this->sanitize($val);

        if (strlen($val) == 10) {
            $area_code = substr($val, 0, 3);
            $part1 = substr($val, 3, 3);
            $part2 = substr($val, 6, 4);
            return '('.$area_code.') '.$part1.'-'.$part2;
        } else {
            return $val;
        }
    }
}
