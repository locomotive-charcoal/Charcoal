<?php

namespace Charcoal\Property;

use PDO;
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;

// From 'charcoal-property'
use Charcoal\Property\AbstractProperty;

/**
 * Date/Time Property
 */
class DateTimeProperty extends AbstractProperty
{
    const DEFAULT_MIN = null;
    const DEFAULT_MAX = null;
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var DateTimeInterface|null
     */
    private $min = self::DEFAULT_MIN;

    /**
     * @var DateTimeInterface|null
     */
    private $max = self::DEFAULT_MAX;

    /**
     * @var string
     */
    private $format = self::DEFAULT_FORMAT;

    /**
     * @return string
     */
    public function type()
    {
        return 'date-time';
    }

    /**
     * AbstractProperty > set_multiple()
     *
     * Ensure multiple can not be true for DateTime property.
     *
     * @param boolean $multiple Multiple flag.
     * @throws InvalidArgumentException If the multiple argument is true (must be false).
     * @return self
     */
    public function setMultiple($multiple)
    {
        $multiple = !!$multiple;
        if ($multiple === true) {
            throw new InvalidArgumentException(
                'Multiple can not be TRUE for date/time property.'
            );
        }
        return $this;
    }

    /**
     * AbstractProperty > multiple()
     *
     * Multiple is always false for Date property.
     *
     * @return boolean
     */
    public function multiple()
    {
        return false;
    }

    /**
     * AbstractProperty > setVal(). Ensure `DateTime` object in val.
     *
     * @param string|DateTimeInterface $val The value to set.
     * @return DateTimeInterface|null
     */
    public function parseOne($val)
    {
        return $this->dateTimeVal($val);
    }

    /**
     * AbstractProperty > inputVal(). Convert `DateTime` to input-friendly string.
     *
     * @todo   [mcaskill: 2016-03-04] Should this be DateTime::RFC3339?
     * @param  mixed $val Optional. The value to to convert for input.
     * @throws Exception If the date/time is invalid.
     * @return string|null
     */
    public function inputVal($val)
    {
        $val = $this->dateTimeVal($val);

        if ($val instanceof DateTimeInterface) {
            return $val->format('Y-m-d H:i:s');
        } elseif (is_string($val)) {
            return $val;
        } else {
            return '';
        }
    }

    /**
     * AbstractProperty > storageVal(). Convert `DateTime` to SQL-friendly string.
     *
     * @param string|DateTime $val Optional. Value to convert to storage format.
     * @throws Exception If the date/time is invalid.
     * @return string|null
     */
    public function storageVal($val)
    {
        $val = $this->dateTimeVal($val);

        if ($val instanceof DateTimeInterface) {
            return $val->format('Y-m-d H:i:s');
        } else {
            if ($this->allowNull()) {
                return null;
            } else {
                throw new Exception(
                    'Invalid date/time value. Must be a DateTimeInterface instance.'
                );
            }
        }
    }

    /**
     * Format `DateTime` to string.
     *
     * > Warning: Passing a value as a parameter sets this value in the objects (calls setVal())
     *
     * @param  mixed $val     The value to to convert for display.
     * @param  array $options Optional display options.
     * @return string
     */
    public function displayVal($val, array $options = [])
    {
        $val = $this->dateTimeVal($val);
        if ($val === null || (is_string($val) && $val === '')) {
            return '';
        }

        if (isset($options['format'])) {
            $format = $options['format'];
        } else {
            $format = $this->format();
        }

        return $val->format($format);
    }

    /**
     * @param string|DateTime|null $min The minimum allowed value.
     * @throws InvalidArgumentException If the date/time is invalid.
     * @return self
     */
    public function setMin($min)
    {
        if ($min === null) {
            $this->min = null;
            return $this;
        }
        if (is_string($min)) {
            try {
                $min = new DateTime($min);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'Can not set min: '.$e->getMessage()
                );
            }
        }
        if (!($min instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid min'
            );
        }
        $this->min = $min;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function min()
    {
        return $this->min;
    }

    /**
     * @param string|DateTime|null $max The maximum allowed value.
     * @throws InvalidArgumentException If the date/time is invalid.
     * @return self
     */
    public function setMax($max)
    {
        if ($max === null) {
            $this->max = null;
            return $this;
        }
        if (is_string($max)) {
            try {
                $max = new DateTime($max);
            } catch (Exception $e) {
                throw new InvalidArgumentException(
                    'Can not set min: '.$e->getMessage()
                );
            }
        }
        if (!($max instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Invalid max'
            );
        }
        $this->max = $max;
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function max()
    {
        return $this->max;
    }

    /**
     * @param string|null $format The date format.
     * @throws InvalidArgumentException If the format is not a string.
     * @return DateTimeProperty Chainable
     */
    public function setFormat($format)
    {
        if ($format === null) {
            $format = '';
        }
        if (!is_string($format)) {
            throw new InvalidArgumentException(
                'Format must be a string'
            );
        }
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function format()
    {
        if ($this->format === null) {
            $this->format = self::DEFAULT_FORMAT;
        }
        return $this->format;
    }

    /**
     * @return array
     */
    public function validationMethods()
    {
        $parent_methods = parent::validationMethods();
        return array_merge($parent_methods, ['min', 'max']);
    }

    /**
     * @return boolean
     */
    public function validateMin()
    {
        $min = $this->min();
        if (!$min) {
            return true;
        }
        $valid = ($this->val() >= $min);
        if ($valid === false) {
            $this->validator()->error('The date is smaller than the minimum value', 'min');
        }
        return $valid;
    }

    /**
     * @return boolean
     */
    public function validateMax()
    {
        $max = $this->max();
        if (!$max) {
            return true;
        }
        $valid = ($this->val() <= $max);
        if ($valid === false) {
            $this->validator()->error('The date is bigger than the maximum value', 'max');
        }
        return $valid;
    }

    /**
     * @return string
     */
    public function sqlExtra()
    {
        return '';
    }

    /**
     * @return string
     */
    public function sqlType()
    {
        return 'DATETIME';
    }

    /**
     * @return integer
     */
    public function sqlPdoType()
    {
        return PDO::PARAM_STR;
    }

    /**
     * @param mixed $val Value to convert to DateTime.
     * @throws InvalidArgumentException If the value is not a valid datetime.
     * @return DateTimeInterface|null
     */
    private function dateTimeVal($val)
    {
        if ($val === null ||
            (is_string($val) && ! strlen(trim($val))) ||
            (is_array($val) && ! count(array_filter($val, 'strlen')))
        ) {
            return null;
        }

        if (is_string($val)) {
            $val = new DateTime($val);
        }

        if (!($val instanceof DateTimeInterface)) {
            throw new InvalidArgumentException(
                'Val must be a valid date'
            );
        }

        return $val;
    }
}
