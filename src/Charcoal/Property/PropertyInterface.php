<?php

namespace Charcoal\Property;

/**
 *
 */
interface PropertyInterface
{
    /**
     * Get the "type" (identifier) of the property.
     * @return string
     */
    public function type();

    /**
     * @return array
     */
    public function fields();

    /**
     * @param array $data The property data.
     * @return PropertyInterface Chainable
     */
    public function setData(array $data);

    /**
     * @param string $ident The property identifier.
     * @return PropertyInterface Chainable
     */
    public function setIdent($ident);

    /**
     * @return string
     */
    public function ident();

    /**
     * @param mixed $val The value.
     * @return PropertyInterface Chainable
     */
    public function setVal($val);

    /**
     * @return mixed
     */
    public function val();

    /**
     * @param string $fieldIdent The property field identifier.
     * @return mixed
     */
    public function fieldVal($fieldIdent);

    /**
     * @param mixed $val Optional. The value to convert to storage value.
     * @return mixed
     */
    public function storageVal($val = null);

    /**
     * @param mixed $val Optional. The value to to convert to display.
     * @return string
     */
    public function displayVal($val = null);

    /**
     * @param mixed $label The property label.
     * @return PropertyInterface Chainable
     */
    public function setLabel($label);

    /**
     * @return boolean
     */
    public function label();

    /**
     * @param boolean $l10n The l10n, or "translatable" flag.
     * @return PropertyInterface Chainable
     */
    public function setL10n($l10n);

    /**
     * @return boolean
     */
    public function l10n();

    /**
     * @param boolean $hidden The hidden flag.
     * @return PropertyInterface Chainable
     */
    public function setHidden($hidden);

    /**
     * @return boolean
     */
    public function hidden();

    /**
     * @param boolean $multiple The multiple flag.
     * @return PropertyInterface Chainable
     */
    public function setMultiple($multiple);

    /**
     * @return boolean
     */
    public function multiple();

    /**
     * Set the multiple options / configuration, when property is `multiple`.
     *
     * ## Options structure
     * - `separator` (string) The separator charactor.
     * - `min` (integer) The minimum number of values. (0 = no limit).
     * - `max` (integer) The maximum number of values. (0 = no limit).
     *
     * @param array $multipleOptions The property multiple options.
     * @return PropertyInterface Chainable
     */
    public function setMultipleOptions(array $multipleOptions);

    /**
     * @return array
     */
    public function multipleOptions();

    /**
     * @param boolean $required The property required flag.
     * @return PropertyInterface Chainable
     */
    public function setRequired($required);

    /**
     * @return boolean
     */
    public function required();

    /**
     * @param boolean $unique The property unique flag.
     * @return PropertyInterface Chainable
     */
    public function setUnique($unique);

    /**
     * @return boolean
     */
    public function unique();

    /**
     * @param boolean $storable The property storable flag.
     * @return PropertyInterface Chainable
     */
    public function setStorable($storable);

    /**
     * @return boolean
     */
    public function storable();

    /**
     * @param boolean $active The property active flag. Inactive properties should have no effects.
     * @return PropertyInterface Chainable
     */
    public function setActive($active);

    /**
     * @return boolean
     */
    public function active();

    /**
     * @return string
     */
    public function sqlExtra();

    /**
     * @return string
     */
    public function sqlType();

    /**
     * @return integer
     */
    public function sqlPdoType();
}
