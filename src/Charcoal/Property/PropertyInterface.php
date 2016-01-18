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
    * @param array $data
    * @return PropertyInterface Chainable
    */
    public function setData(array $data);

    /**
    * @param string $ident
    * @return PropertyInterface Chainable
    */
    public function setIdent($ident);

    /**
    * @return string
    */
    public function ident();

    /**
    * @param mixed $val
    * @return PropertyInterface Chainable
    */
    public function setVal($val);

    /**
    * @return mixed
    */
    public function val();

    /**
    * @param string $fieldIdent
    * @return mixed
    */
    public function fieldVal($fieldIdent);

    /**
    * @param mixed $val
    * @return mixed
    */
    public function storageVal($val = null);

    /**
    * @param mixed $label
    * @return PropertyInterface Chainable
    */
    public function setLabel($label);

    /**
    * @return boolean
    */
    public function label();

    /**
    * @param boolean $l10n
    * @return PropertyInterface Chainable
    */
    public function setL10n($l10n);

    /**
    * @return boolean
    */
    public function l10n();

    /**
    * @param boolean $hidden
    * @return PropertyInterface Chainable
    */
    public function setHidden($hidden);

    /**
    * @return boolean
    */
    public function hidden();

    /**
    * @param boolean $multiple
    * @return PropertyInterface Chainable
    */
    public function setMultiple($multiple);

    /**
    * @return boolean
    */
    public function multiple();

    /**
    * @param array $multipleOptions
    * @return PropertyInterface Chainable
    */
    public function setMultipleOptions(array $multipleOptions);

    /**
    * @return array
    */
    public function multipleOptions();

    /**
    * @param boolean $required
    * @return PropertyInterface Chainable
    */
    public function setRequired($required);

    /**
    * @return boolean
    */
    public function required();

    /**
    * @param boolean $unique
    * @return PropertyInterface Chainable
    */
    public function setUnique($unique);

    /**
    * @return boolean
    */
    public function unique();

    /**
    * @param boolean $storable
    * @return PropertyInterface Chainable
    */
    public function setStorable($storable);

    /**
    * @return boolean
    */
    public function storable();

    /**
    * @param boolean $active
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
