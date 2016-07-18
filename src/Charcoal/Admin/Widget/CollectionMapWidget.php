<?php

namespace Charcoal\Admin\Widget;

use \Charcoal\Factory\FactoryInterface;

use \Charcoal\Loader\CollectionLoader;

use \Charcoal\Admin\AdminWidget;

use \Charcoal\Presenter\Presenter;

use \Charcoal\App\App;

/**
 *
 */
class CollectionMapWidget extends AdminWidget
{
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
     * @var string $infoboxTemplate
     */
    public $infoboxTemplate = '';

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
            $loader = new CollectionLoader([
                'logger'    => $this->logger,
                'factory'   => $this->modelFactory()
            ]);
            $loader->setModel($this->objProto());

            $that = $this;
            $loader->setCallback(function($obj) use ($that) {
                $obj->mapInfoboxTemplate = $that->infoboxTemplate();

                if ($that->latProperty() && $that->latProperty()) {
                    $obj->mapShowMarker = true;
                    $obj->mapLat = call_user_func([$obj, $that->latProperty()]);
                    $obj->mapLon = call_user_func([$obj, $that->lonProperty()]);
                } else {
                    $obj->mapShowMarker = false;
                }

                if ($that->polygonProperty()) {
                    $mapPolygon = call_user_func([$obj, $that->polygonProperty()]);
                    if ($mapPolygon) {
                        $obj->mapShowPolygon = true;
                        $obj->mapPolygon = $that->formatPolygon($mapPolygon);
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
                $ret[] = [(float)$coords[0], (float)$coords[1]];
            }
        } else {
            $ret = $rawPolygon;
        }
        return json_encode($ret, true);
    }

    /**
     * Google maps api key.
     *
     * @return string|null Google maps api key.
     */
    public function gmapApiKey()
    {
        $appConfig = App::instance()->config();
        $key = $appConfig->get('apis.google.map.key');

        return ($key) ? $key : null;
    }
}
