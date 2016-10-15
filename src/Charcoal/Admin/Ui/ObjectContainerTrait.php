<?php

namespace Charcoal\Admin\Ui;

use \Exception;
use \RuntimeException;
use \InvalidArgumentException;

use \Charcoal\Factory\FactoryInterface;

/**
* Fully implements ObjectContainerInterface
*/
trait ObjectContainerTrait
{
    /**
     * @var FactoryInterface $modelFactory
     */
    private $modelFactory;

    /**
     * @var string $objType
     */
    protected $objType;

    /**
     * @var string $objId
     */
    protected $objId;

    /**
     * @var string $objBaseClass
     */
    protected $objBaseClass;

    /**
     * @var Object $obj
     */
    protected $obj;

    /**
     * @param FactoryInterface $factory The model factory, to create objects.
     * @return ObjectContainerInterface Chainable
     */
    public function setModelFactory(FactoryInterface $factory)
    {
        $this->modelFactory = $factory;
        return $this;
    }

    /**
     * @throws Exception If the model factory was not set before being accessed.
     * @return FactoryInterface
     */
    protected function modelFactory()
    {
        if ($this->modelFactory === null) {
            throw new Exception(
                sprintf('Model factory not set for %s', get_class($this))
            );
        }
        return $this->modelFactory;
    }

    /**
     * @param string $objType The object type.
     * @throws InvalidArgumentException If provided argument is not of type 'string'.
     * @return ObjectContainerInterface Chainable
     */
    public function setObjType($objType)
    {
        if (!is_string($objType)) {
            throw new InvalidArgumentException(
                'Obj type needs to be a string'
            );
        }
        $this->objType = str_replace(['.', '_'], '/', $objType);
        return $this;
    }

    /**
     * @return string
     */
    public function objType()
    {
        return $this->objType;
    }

    /**
     * @param string|numeric $objId The object id to load.
     * @throws InvalidArgumentException If provided argument is not of type 'scalar'.
     * @return ObjectContainerInterface Chainable
     */
    public function setObjId($objId)
    {
        if (!is_scalar($objId)) {
            throw new InvalidArgumentException(
                'Obj ID must be a string or numerical value.'
            );
        }
        $this->objId = $objId;
        return $this;
    }

    /**
     * Assign the Object ID
     *
     * @return string|numeric
     */
    public function objId()
    {
        return $this->objId;
    }

    /**
     * @param string $objBaseClass The base class.
     * @throws InvalidArgumentException If provided argument is not of type 'string'.
     * @return ObjectContainerInterface Chainable
     */
    public function setObjBaseClass($objBaseClass)
    {
        if (!is_string($objBaseClass)) {
            throw new InvalidArgumentException(
                'Base class must be a string.'
            );
        }
        $this->objBaseClass = $objBaseClass;
        return $this;
    }

    /**
     * @return string|null
     */
    public function objBaseClass()
    {
        return $this->objBaseClass;
    }

    /**
     * Retrieve the object.
     *
     * @return ModelInterface
     */
    public function obj()
    {
        if ($this->obj === null) {
            $this->obj = $this->createOrLoadObj();
        }

        return $this->obj;
    }

    /**
     * Create or load the object.
     *
     * @return ModelInterface
     */
    protected function createOrLoadObj()
    {
        if ($this->objId()) {
            return $this->loadObj();
        } elseif (!empty($_GET['clone_id'])) {
            return $this->cloneObj();
        } elseif (!empty($_GET['blueprint_id'])) {
            return $this->createObjFromBluePrint();
        } else {
            return $this->createObj();
        }
    }

    /**
     * @throws Exception If the object is not valid.
     * @return ModelInterface
     */
    protected function cloneObj()
    {
        if (empty($_GET['clone_id'])) {
            throw new Exception(
                sprintf(
                    '%1$s cannot clone object. Clone ID missing from request.',
                    get_class($this)
                )
            );
        }

        $obj   = $this->createObj();
        $clone = $this->createObj()->load($_GET['clone_id']);

        $data = $clone->data();
        unset($data[$clone->key()]);

        // This is the actual key we want to remove.
        unset($data[$obj->key()]);

        // Set the right data
        $obj->setData($data);

        return $obj;
    }

    /**
     * @throws Exception If the object is not valid.
     * @return ModelInterface
     */
    protected function createObjFromBluePrint()
    {
        if (empty($_GET['blueprint_id'])) {
            throw new Exception(
                sprintf(
                    '%1$s cannot create object from blueprint. Blueprint ID missing from request.',
                    get_class($this)
                )
            );
        }

        $obj = $this->createObj();

        $blueprint = $this->modelFactory()->create($obj->blueprintType());
        $blueprint->load($_GET['blueprint_id']);

        $data = $blueprint->data();
        unset($data[$blueprint->key()]);

        // This is the actual key we want to remove.
        unset($data[$obj->key()]);

        // Set the right data
        $obj->setData($data);

        return $obj;
    }

    /**
     * @throws Exception If the object is not valid.
     * @return ModelInterface
     */
    protected function createObj()
    {
        if (!$this->validateObjType()) {
            throw new Exception(
                sprintf(
                    '%1$s cannot create object. Invalid object type: "%2$s"',
                    get_class($this),
                    $this->objType()
                )
            );
        }

        $objType = $this->objType();
        $obj = $this->modelFactory()->create($objType);

        return $obj;
    }

    /**
     * @return ModelInterface The loaded object
     */
    protected function loadObj()
    {
        if ($this->obj === null) {
            $this->obj = $this->createObj();
        }
        $obj = $this->obj;

        $objId = $this->objId();
        if (!$objId) {
            return $obj;
        }
        $obj->load($objId);
        return $obj;
    }

    /**
     * @throws RuntimeException If the object is invalid.
     * @return boolean
     */
    protected function validateObjType()
    {
        try {
            $objType = $this->objType();
            // Catch exception to know if the objType is valid

            $obj = $this->modelFactory()->get($objType);
            if (!$this->validateObjBaseClass($obj)) {
                throw new RuntimeException(
                    sprintf(
                        'Can not create object, type is not an instance of %s',
                        $this->objBaseClass()
                    )
                );
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $obj Object to validate.
     * @return boolean
     */
    protected function validateObjBaseClass($obj)
    {
        $objBaseClass = $this->objBaseClass();
        if (!$objBaseClass) {
            // If no base class is set, then no validation is performed.
            return true;
        }

        try {
            return ($obj instanceof $objBaseClass);
        } catch (Exception $e) {
            return false;
        }
    }
}
