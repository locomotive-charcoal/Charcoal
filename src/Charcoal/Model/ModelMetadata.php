<?php

namespace Charcoal\Model;

use \InvalidArgumentException as InvalidArgumentException;

use \Charcoal\Metadata\AbstractMetadata as AbstractMetadata;

use \Charcoal\Charcoal as Charcoal;
use \Charcoal\Helper\Cache as Cache;

class ModelMetadata extends AbstractMetadata implements \ArrayAccess
{

    /**
     * @var string $_ident
     */
    private $_ident;

    private $_sources;
    private $_default_source;

    /**
    * The actual config data
    * @var array $data
    */
    public $data;

    /**
    * @param array $data
    * @throws InvalidArgumentException
    * @return ModelMetadata Chainable
    */
    public function set_data($data)
    {
        if (!is_array($data)) {
            throw new InvalidArgumentException('Data must be an array');
        }
        parent::set_data($data);

        if (isset($data['ident']) && $data['ident'] !== null) {
            $this->set_ident($data['ident']);
        }
        if (isset($data['sources']) && $data['sources'] !== null) {
            $this->set_sources($data['sources']);
        }
        if (isset($data['default_source']) && $data['default_source'] !== null) {
            $this->set_default_source($data['default_source']);
        }

        return $this;
    }

    /**
    * @param string $ident
    * @throws InvalidArgumentException if ident is not a string
    * @return ModelMetadata Chainable
    */
    public function set_ident($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(__CLASS__.'::'.__FUNCTION__.'Ident must be a string');
        }
        $this->_ident = $ident;
        return $this;
    }

    /**
    * @return string
    */
    public function ident()
    {
        return $this->_ident;
    }

    /**
    * @param array $sources
    * @throws InvalidArgumentException
    * @return ModelMetadata Chainable
    */
    public function set_sources($sources)
    {
        if (!is_array($sources)) {
            throw new InvalidArgumentException('Sources must be an array');
        }
        foreach ($sources as $source_ident => $source) {
            $this->add_source($source_ident, $source);
        }
        return $this;
    }

    /**
    * @return array
    */
    public function sources()
    {
        return $this->_sources;
    }

    /**
    * @param string $source_ident
    * @param mixed  $source
    * @return ModelMetadata Chainable
    */
    public function add_source($source_ident, $source)
    {
        $this->_sources[$source_ident] = $source;
        return $this;
    }

    /**
    * @param string $source_ident
    * @return mixed
    */
    public function source($source_ident)
    {
        return $this->_sources[$source_ident];
    }

    /**
    * @param string $default_source
    * @throws InvalidArgumentException
    * @return ModelMetadata Chainable
    */
    public function set_default_source($default_source)
    {
        if (!is_string($default_source)) {
            throw new InvalidArgumentException('Default source needs to be a string');
        }
        $this->_default_source = $default_source;
        return $this;
    }

    /**
    * @return string
    */
    public function default_source()
    {
        return $this->_default_source;
    }
}
