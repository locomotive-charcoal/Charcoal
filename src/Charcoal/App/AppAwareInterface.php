<?php

namespace Charcoal\App;

use \Charcoal\App\AppInterface;

/**
 * Interface for objects that depend on an app.
 *
 * Mostly exists to avoid boilerplate code duplication.
 */
interface AppAwareInterface
{
    /**
     * @param AppInterface $app The app instance this object depends on.
     * @return AppAwareInterface Chainable
     */
    public function setApp(AppInterface $app);

    /**
     * @return AppInterface
     */
    //protected function app();
}
