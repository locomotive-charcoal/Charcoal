<?php

namespace Charcoal\Admin\Template;

// Intra-module (`charcoal-admin`) dependencies
use Charcoal\Admin\Ui\DashboardContainerInterface;
use Charcoal\Admin\Ui\DashboardContainerTrait;
use Charcoal\Admin\Widget\DashboardWidget;

// Local parent namespace dependencies
use Charcoal\Admin\AdminTemplate;

/**
 * The Home template is a simple Dashboard, loaded from the metadata.
 */
class HomeTemplate extends AdminTemplate implements DashboardContainerInterface
{
    use DashboardContainerTrait;

    /**
     * @param array $data Optional dashboard data.
     * @return Charcoal\Ui\Dashboard\DashboardInterface
     */
    public function createDashboardConfig(array $data = null)
    {
        return $this->dashboardConfig();
    }
}
