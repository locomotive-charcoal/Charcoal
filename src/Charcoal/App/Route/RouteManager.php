<?php

namespace Charcoal\App\Route;

use \InvalidArgumentException;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Local namespace dependencies
use \Charcoal\App\AbstractManager;

/**
 *
 */
class RouteManager extends AbstractManager
{
    /**
     * Set up the routes
     *
     * There are 3 types of routes:
     *
     * - Templates
     * - Actions
     * - Scripts
     *
     * @return void
     */
    public function setupRoutes()
    {
        $routes    = $this->config();

        if (PHP_SAPI == 'cli') {
            $scripts = ( isset($routes['scripts']) ? $routes['scripts'] : [] );
            foreach ($scripts as $scriptIdent => $scriptConfig) {
                $this->setupScript($scriptIdent, $scriptConfig);
            }
        } else {
            $templates = ( isset($routes['templates']) ? $routes['templates'] : [] );
            foreach ($templates as $templateIdent => $templateConfig) {
                $this->setupTemplate($templateIdent, $templateConfig);
            }

            $actions = ( isset($routes['actions']) ? $routes['actions'] : [] );
            foreach ($actions as $actionIdent => $actionConfig) {
                $this->setupAction($actionIdent, $actionConfig);
            }
        }
    }

    /**
     * @param string             $templateIdent  The template identifier.
     * @param array|\ArrayAccess $templateConfig The template config.
     * @throws InvalidArgumentException If the ident is not a string.
     * @return void
     */
    public function setupTemplate($templateIdent, $templateConfig)
    {
        if (!is_string($templateIdent)) {
            throw new InvalidArgumentException(
                'Can not setup route template, template ident is not a string'
            );
        }

        $app = $this->app();
        $templateIdent = ltrim($templateIdent, '/');

        if (!isset($templateConfig['ident'])) {
            $templateConfig['ident'] = $templateIdent;
        }

        if (isset($templateConfig['route'])) {
            $routeIdent = '/'.ltrim($templateConfig['route'], '/');
        } else {
            $routeIdent = '/'.$templateIdent;
            $templateConfig['route'] = $routeIdent;
        }

        if (isset($templateConfig['methods'])) {
            $methods = $templateConfig['methods'];
        } else {
            $methods = ['GET'];
        }

        $routeHandler = $app->map(
            $methods,
            $routeIdent,
            function (
                RequestInterface $request,
                ResponseInterface $response,
                array $args = []
            ) use (
                $templateIdent,
                $templateConfig
            ) {
                $this['logger']->debug(
                    sprintf('Loaded template route: %s', $templateIdent),
                    $templateConfig
                );

                if (!isset($templateConfig['template_data'])) {
                    $templateConfig['template_data'] = [];
                }

                if (count($args)) {
                    $templateConfig['template_data'] = array_merge(
                        $templateConfig['template_data'],
                        $args
                    );
                }

                $routeFactory = $this['route/factory'];
                $defaultRoute = 'charcoal/app/route/template';
                $routeController = isset($templateConfig['route_controller'])
                    ? $templateConfig['route_controller']
                    : $defaultRoute;

                $route = $routeFactory->create($routeController, [
                    'config' => $templateConfig,
                    'logger' => $this['logger']
                ]);

                return $route($this, $request, $response);
            }
        );

        if (isset($templateConfig['ident'])) {
            $routeHandler->setName($templateConfig['ident']);
        }

        if (isset($templateConfig['template_data'])) {
            $routeHandler->setArguments($templateConfig['template_data']);
        }
    }

    /**
     * @param string             $actionIdent  The action identifier.
     * @param array|\ArrayAccess $actionConfig The action config.
     * @throws InvalidArgumentException If the ident is not a string.
     * @return void
     */
    public function setupAction($actionIdent, $actionConfig)
    {
        if (!is_string($actionIdent)) {
            throw new InvalidArgumentException(
                'Can not setup route action, action ident is not a string'
            );
        }

        $app = $this->app();
        $actionIdent = ltrim($actionIdent, '/');

        if (!isset($actionConfig['ident'])) {
            $actionConfig['ident'] = $actionIdent;
        }

        if (isset($actionConfig['route'])) {
            $routeIdent = '/'.ltrim($actionConfig['route'], '/');
        } else {
            $routeIdent = '/'.$actionIdent;
            $actionConfig['route'] = $routeIdent;
        }

        if (isset($actionConfig['methods'])) {
            $methods = $actionConfig['methods'];
        } else {
            $methods = ['POST'];
        }

        $routeHandler = $app->map(
            $methods,
            $routeIdent,
            function (
                RequestInterface $request,
                ResponseInterface $response,
                array $args = []
            ) use (
                $actionIdent,
                $actionConfig
            ) {
                $this['logger']->debug(
                    sprintf('Loaded action route: %s', $actionIdent),
                    $actionConfig
                );

                if (!isset($actionConfig['action_data'])) {
                    $actionConfig['action_data'] = [];
                }

                if (count($args)) {
                    $actionConfig['action_data'] = array_merge(
                        $actionConfig['action_data'],
                        $args
                    );
                }

                $routeFactory = $this['route/factory'];
                $defaultRoute = 'charcoal/app/route/action';
                $routeController = isset($actionConfig['route_controller'])
                    ? $actionConfig['route_controller']
                    : $defaultRoute;

                $route = $routeFactory->create($routeController, [
                    'config' => $actionConfig,
                    'logger' => $this['logger']
                ]);

                return $route($this, $request, $response);
            }
        );

        if (isset($actionConfig['ident'])) {
            $routeHandler->setName($actionConfig['ident']);
        }

        if (isset($actionConfig['action_data'])) {
            $routeHandler->setArguments($actionConfig['action_data']);
        }
    }

    /**
     * @param string             $scriptIdent  The script identifier.
     * @param array|\ArrayAccess $scriptConfig The script config.
     * @throws InvalidArgumentException If the ident is not a string.
     * @return void
     */
    public function setupScript($scriptIdent, $scriptConfig)
    {
        if (!is_string($scriptIdent)) {
            throw new InvalidArgumentException(
                'Can not setup route script, script ident is not a string'
            );
        }

        $app = $this->app();
        $scriptIdent = ltrim($scriptIdent, '/');

        if (!isset($scriptConfig['ident'])) {
            $scriptConfig['ident'] = $scriptIdent;
        }

        if (isset($scriptConfig['route'])) {
            $routeIdent = '/'.ltrim($scriptConfig['route'], '/');
        } else {
            $routeIdent = '/'.$scriptIdent;
            $scriptConfig['route'] = $routeIdent;
        }

        if (isset($scriptConfig['methods'])) {
            $methods = $scriptConfig['methods'];
        } else {
            $methods = ['GET'];
        }

        $routeHandler = $app->map(
            $methods,
            $routeIdent,
            function (
                RequestInterface $request,
                ResponseInterface $response,
                array $args = []
            ) use (
                $scriptIdent,
                $scriptConfig
            ) {
                $this->logger->debug(
                    sprintf('Loaded script route: %s', $scriptIdent),
                    $scriptConfig
                );

                if (!isset($scriptConfig['script_data'])) {
                    $scriptConfig['script_data'] = [];
                }

                if (count($args)) {
                    $scriptConfig['script_data'] = array_merge(
                        $scriptConfig['script_data'],
                        $args
                    );
                }

                $routeFactory = $this['route/factory'];
                $defaultRoute = 'charcoal/app/route/script';
                $routeController = isset($scriptConfig['route_controller'])
                    ? $scriptConfig['route_controller']
                    : $defaultRoute;

                $route = $routeFactory->create($routeController, [
                    'config' => $scriptConfig,
                    'logger' => $this['logger']
                ]);

                return $route($this, $request, $response);
            }
        );

        if (isset($scriptConfig['ident'])) {
            $routeHandler->setName($scriptConfig['ident']);
        }

        if (isset($scriptConfig['script_data'])) {
            $routeHandler->setArguments($scriptConfig['script_data']);
        }
    }
}
