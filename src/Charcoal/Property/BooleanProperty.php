<?php

namespace Charcoal\Property;

use PDO;
use InvalidArgumentException;

// From 'charcoal-translation'
use Charcoal\Translator\Translation;

// From 'charcoal-property'
use Charcoal\Property\AbstractProperty;

/**
 * Boolean Property
 */
class BooleanProperty extends AbstractProperty
{
    /**
     * The label for "true".
     *
     * @var Translation
     */
    private $trueLabel;

    /**
     * The label for "false".
     *
     * @var Translation
     */
    private $falseLabel;

    /**
     * @return string
     */
    public function type()
    {
        return 'boolean';
    }

    /**
     * @param mixed $val A single value to parse.
     * @return boolean
     */
    public function parseOne($val)
    {
        return !!$val;
    }

    /**
     * @param  mixed $val     The value to to convert for display.
     * @param  array $options Optional display options.
     * @return string
     */
    public function displayVal($val, array $options = [])
    {
        if ($val === true) {
            if (isset($options['true_label'])) {
                $label = $options['true_label'];
            } else {
                $label = $this->trueLabel();
            }
        } else {
            if (isset($options['false_label'])) {
                $label = $options['false_label'];
            } else {
                $label = $this->falseLabel();
            }
        }

        return $this->translator()->translate($label);
    }

    /**
     * AbstractProperty > setMultiple()
     *
     * Ensure multiple can not be true for DateTime property.
     *
     * @param boolean $multiple The multiple flag.
     * @throws InvalidArgumentException If multiple is true. (must be false for boolean properties).
     * @return self
     */
    public function setMultiple($multiple)
    {
        $multiple = !!$multiple;
        if ($multiple === true) {
            throw new InvalidArgumentException(
                'Multiple can not be true for boolean property.'
            );
        }
        return $this;
    }

    /**
     * AbstractProperty > multiple()
     *
     * Multiple is always false for Boolean property.
     *
     * @return boolean
     */
    public function multiple()
    {
        return false;
    }

    /**
     * @param mixed $label The true label.
     * @return self
     */
    public function setTrueLabel($label)
    {
        $this->trueLabel = $this->translator()->translation($label);
        return $this;
    }

    /**
     * @return Translation
     */
    public function trueLabel()
    {
        if ($this->trueLabel === null) {
            // Default value
            $this->setTrueLabel('True');
        }
        return $this->trueLabel;
    }

    /**
     * @param mixed $label The false label.
     * @return self
     */
    public function setFalseLabel($label)
    {
        $this->falseLabel = $this->translator()->translation($label);
        return $this;
    }

    /**
     * @return Translation
     */
    public function falseLabel()
    {
        if ($this->falseLabel === null) {
            // Default value
            $this->setFalseLabel('False');
        }
        return $this->falseLabel;
    }

    /**
     * @return string
     */
    public function sqlExtra()
    {
        return '';
    }

    /**
     * Get the SQL type (Storage format).
     *
     * Boolean properties are stored as `TINYINT(1) UNSIGNED`
     *
     * @return string The SQL type
     */
    public function sqlType()
    {
        $dbDriver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($dbDriver === 'sqlite') {
            return 'INT';
        } else {
            return 'TINYINT(1) UNSIGNED';
        }
    }

    /**
     * @return integer
     */
    public function sqlPdoType()
    {
        return PDO::PARAM_BOOL;
    }

    /**
     * @return array
     */
    public function choices()
    {
        $val = $this->val();
        return [
            [
                'label'    => $this->trueLabel(),
                'selected' => !!$val,
                'value'    => 1
            ],
            [
                'label'    => $this->falseLabel(),
                'selected' => !$val,
                'value'    => 0
            ]
        ];
    }
}
