<?php

namespace Charcoal\Admin\Template;

use Charcoal\Property\FileProperty;
use \RuntimeException;

// Dependency from Pimple
use \Pimple\Container;

// Dependency from 'charcoal-core'
use \Charcoal\Factory\FactoryInterface;

// Dependency from 'charcoal-translation'
use \Charcoal\Translation\TranslationConfig;

// Local parent namespace dependencies
use \Charcoal\Admin\AdminTemplate;

/**
 *
 */
class ElfinderTemplate extends AdminTemplate
{
    /**
     * Store the factory instance for the current class.
     *
     * @var FactoryInterface
     */
    private $propertyFactory;

    /**
     * Store the current property instance for the current class.
     *
     * @var PropertyInterface
     */
    private $formProperty;

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->setPropertyFactory($container['property/factory']);
    }

    /**
     * Set a property factory.
     *
     * @param FactoryInterface $factory The property factory,
     *     to createable property values.
     * @return self
     */
    protected function setPropertyFactory(FactoryInterface $factory)
    {
        $this->propertyFactory = $factory;

        return $this;
    }

    /**
     * Retrieve the property factory.
     *
     * @throws RuntimeException If the property factory was not previously set.
     * @return FactoryInterface
     */
    public function propertyFactory()
    {
        if (!isset($this->propertyFactory)) {
            throw new RuntimeException(
                sprintf('Property Factory is not defined for "%s"', get_class($this))
            );
        }

        return $this->propertyFactory;
    }

    /**
     * @return string
     */
    public function elfinderUrl()
    {
        return $this->baseUrl().'assets/admin/elfinder/';
    }

    /**
     * @return string
     */
    public function elfinderAssets()
    {
        return isset($_GET['assets']) ? (bool)$_GET['assets'] : true;
    }

    /**
     * @return string
     */
    public function elfinderCallback()
    {
        return isset($_GET['callback']) ? $_GET['callback'] : '';
    }

    /**
     * Retrieve the current object type from the GET parameters.
     *
     * @return string
     */
    public function objType()
    {
        return (isset($_GET['obj_type']) ? $_GET['obj_type'] : null);
    }

    /**
     * Retrieve the current object ID from the GET parameters.
     *
     * @return string
     */
    public function objId()
    {
        return (isset($_GET['obj_id']) ? $_GET['obj_id'] : null);
    }

    /**
     * Retrieve the current object's property identifier from the GET parameters.
     *
     * @return string
     */
    public function propertyIdent()
    {
        return (isset($_GET['property']) ? $_GET['property'] : null);
    }

    /**
     * Retrieve the current property.
     *
     * @return PropertyInterface
     */
    public function formProperty()
    {
        if ($this->formProperty === null) {
            $this->formProperty = false;

            if ($this->objType() && $this->propertyIdent()) {
                $propertyIdent = $this->propertyIdent();

                $model = $this->modelFactory()->create($this->objType());
                $props = $model->metadata()->properties();

                if (isset($props[$propertyIdent])) {
                    $propertyMetadata = $props[$propertyIdent];

                    $property = $this->propertyFactory()->create($propertyMetadata['type']);

                    $property->setIdent($propertyIdent);
                    $property->setData($propertyMetadata);

                    $this->formProperty = $property;
                }
            }
        }

        return $this->formProperty;
    }

    /**
     * Retrieve the current property's client-side settings for elFinder.
     *
     * @return string Returns data serialized with {@see json_encode()}.
     */
    public function elfinderPropertyConfig()
    {
        $property = $this->formProperty();
        $settings = [];

        if ($property) {
            $translator = TranslationConfig::instance();

            $settings['lang'] = $translator->currentLanguage();

            if($property instanceof FileProperty) {
                $settings['onlyMimes'] = $property->acceptedMimetypes();
            } elseif (isset($_GET['filetype'])) {
                $settings['onlyMimes'] = (array)filter_input(INPUT_GET, 'filetype', FILTER_SANITIZE_STRING);
            }

            $settings['rememberLastDir'] = !($property instanceof FileProperty);

        }

        return json_encode($settings, (JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
    }
}
