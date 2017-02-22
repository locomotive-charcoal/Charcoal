<?php

namespace Charcoal\Admin\Widget;

use RuntimeException;

// From Pimple
use Pimple\Container;

// From 'charcoal-core'
use Charcoal\Loader\CollectionLoader;

// From 'charcoal-admin'
use Charcoal\Admin\AdminWidget;

/**
 *
 */
class CollectionMapWidget extends AdminWidget
{
    /**
     * The API key for the mapping service.
     *
     * @var array
     */
    private $apiKey;

    /**
     * @var \Charcoal\Model\AbstractModel[] $mapObjects
     */
    private $mapObjects;

    /**
     * @var \Charcoal\Model\AbstractModel $objProto
     */
    private $objProto;

    /**
     * The ident of the object's property for the latitude.
     * @var string $latProperty
     */
    private $latProperty;

    /**
     * The ident of the object's property for the longitude.
     * @var string $latProperty
     */
    private $lonProperty;

    /**
     * @var string $polygonProperty
     */
    private $polygonProperty;

    /**
     * @var string $pathProperty
     */
    private $pathProperty;

    /**
     * Store the collection loader for the current class.
     *
     * @var CollectionLoader
     */
    private $collectionLoader;

    /**
     * @var string $infoboxTemplate
     */
    public $infoboxTemplate = '';

    /**
     * Inject dependencies from a DI Container.
     *
     * @param  Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);

        $this->collectionLoader = $container['model/collection/loader'];

        $appConfig = $container['config'];

        if (isset($appConfig['google.console.api_key'])) {
            $this->setApiKey($appConfig['google.console.api_key']);
        } elseif (isset($appConfig['apis.google.map.key'])) {
            $this->setApiKey($appConfig['apis.google.map.key']);
        }
    }

    /**
     * Sets the API key for the mapping service.
     *
     * @param  string $key An API key.
     * @return self
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;

        return $this;
    }

    /**
     * Retrieve API key for the mapping service.
     *
     * @return string
     */
    public function apiKey()
    {
        return $this->apiKey;
    }

    /**
     * Retrieve the model collection loader.
     *
     * @throws RuntimeException If the collection loader was not previously set.
     * @return CollectionLoader
     */
    protected function collectionLoader()
    {
        if (!isset($this->collectionLoader)) {
            throw new RuntimeException(sprintf(
                'Collection Loader is not defined for "%s"',
                get_class($this)
            ));
        }

        return $this->collectionLoader;
    }

    /**
     * @return \Charcoal\Model\AbstractModel
     */
    private function objProto()
    {
        if ($this->objProto === null) {
            $this->objProto = $this->modelFactory()->create($this->{'obj_type'});
        }
        return $this->objProto;
    }

    /**
     * @param string $p The latitude property ident.
     * @return MapWidget Chainable
     */
    public function setLatProperty($p)
    {
        $this->latProperty = $p;
        return $this;
    }

    /**
     * @return string
     */
    public function latProperty()
    {
        return $this->latProperty;
    }

    /**
     * @param string $p The longitude property ident.
     * @return MapWidget Chainable
     */
    public function setLonProperty($p)
    {
        $this->lonProperty = $p;
        return $this;
    }

    /**
     * @return string
     */
    public function lonProperty()
    {
        return $this->lonProperty;
    }

    /**
     * @param string $p The polygon property ident.
     * @return MapWidget Chainable
     */
    public function setPolygonProperty($p)
    {
        $this->polygonProperty = $p;
        return $this;
    }

    /**
     * @return string
     */
    public function polygonProperty()
    {
        return $this->polygonProperty;
    }

    /**
     * @param string $p The path property ident.
     * @return MapWidget Chainable
     */
    public function setPathProperty($p)
    {
        $this->pathProperty = $p;
        return $this;
    }

    /**
     * @return string
     */
    public function pathProperty()
    {
        return $this->pathProperty;
    }

    /**
     * @param string $template The infobox template ident.
     * @return CollectionMapWidget Chainable
     */
    public function setInfoboxTemplate($template)
    {
        $this->infoboxTemplate = $template;
        return $this;
    }

    /**
     * @return string
     */
    public function infoboxTemplate()
    {
        return $this->infoboxTemplate;
    }

    /**
     * Return all the objs with geographical information
     *
     * @return Collection
     */
    public function mapObjects()
    {
        if ($this->mapObjects === null) {
            $loader = $this->collectionLoader();
            $loader->setModel($this->objProto());

            $that = $this;
            $loader->setCallback(function(&$obj) use ($that) {
                $obj->mapInfoboxTemplate = $that->infoboxTemplate();

                if ($that->latProperty() && $that->latProperty()) {
                    $obj->mapShowMarker = true;
                    $obj->mapLat = call_user_func([ $obj, $that->latProperty() ]);
                    $obj->mapLon = call_user_func([ $obj, $that->lonProperty() ]);

                    if (!$obj->mapLat || !$obj->mapLon) {
                        $obj = null;
                    }
                } else {
                    $obj->mapShowMarker = false;
                }

                if ($that->pathProperty()) {
                    $mapPath = call_user_func([$obj, $that->pathProperty()]);
                    if ($mapPath) {
                        $obj->mapShowPath = true;
                        // Same type of coords.
                        $obj->mapPath = $that->formatPolygon($mapPath);

                        if (!$obj->mapPath) {
                            $obj = null;
                        }
                    } else {
                        $obj->mapShowPath = false;
                    }
                }

                if ($that->polygonProperty()) {
                    $mapPolygon = call_user_func([$obj, $that->polygonProperty()]);
                    if ($mapPolygon) {
                        $obj->mapShowPolygon = true;
                        $obj->mapPolygon = $that->formatPolygon($mapPolygon);

                        if (!$obj->mapPolygon) {
                            $obj = null;
                        }
                    } else {
                        $obj->mapShowPolygon = false;
                    }
                }
            });

            $this->mapObjects = $loader->load();
        }

        foreach ($this->mapObjects as $obj) {
            $GLOBALS['widget_template'] = $obj->mapInfoboxTemplate;
            yield $obj;
        }
    }

    /**
     * @return boolean
     */
    public function showInfobox()
    {
        return ($this->infoboxTemplate != '');
    }

    /**
     * @param mixed $rawPolygon The polygon information.
     * @return string
     */
    private function formatPolygon($rawPolygon)
    {
        if (is_string($rawPolygon)) {
            $polygon = explode(' ', $rawPolygon);
            $ret = [];
            foreach ($polygon as $poly) {
                $coords = explode(',', $poly);
                if (count($coords) < 2) {
                    continue;
                }
                $ret[] = [(float)$coords[0], (float)$coords[1]];
            }
        } else {
            $ret = $rawPolygon;
        }
        return json_encode($ret, true);
    }
}
