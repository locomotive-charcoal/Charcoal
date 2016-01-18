<?php

namespace Charcoal\Property;

use \InvalidArgumentException;

// Intra-module (`charcoal-core`) dependencies
use \Charcoal\Model\AbstractMetadata;

/**
*
*/
class PropertyMetadata extends AbstractMetadata
{
    /**
     * @var string $_ident
     */
    private $ident;

    /**
    * The actual config data
    * @var array $data
    */
    public $data;

    /**
    * @param string $ident
    * @throws InvalidArgumentException if the ident is not a string
    * @return PropertyMetadata Chainable
    */
    public function setIdent($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                __CLASS__.'::'.__FUNCTION__.' - Ident must be a string.'
            );
        }
        $this->ident = $ident;

        return $this;
    }

    /**
    * @return string
    */
    public function ident()
    {
        return $this->ident;
    }
}
