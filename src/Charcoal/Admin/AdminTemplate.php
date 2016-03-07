<?php

namespace Charcoal\Admin;

use \ArrayIterator;
use \Exception;
use \InvalidArgumentException;

use \Pimple\Container;

use \Charcoal\App\Template\AbstractTemplate;
use \Charcoal\Translation\TranslationString;
use \Charcoal\Admin\User;
use \Charcoal\Admin\Object\AuthToken;

/**
 * Base class for all `admin` Templates.
 *
 * An action extends
 *
 * # Available (mustache) methods
 * - `title` (TranslationString) - The page title
 * - `subtitle` (TranslationString) The page subtitle
 * - `showHeaderMenu` (bool) - Display the header menu or not
 * - `headerMenu` (iterator) - The header menu data
 * - `showFooterMenu` (bool) - Display the footer menu or not
 * - `footerMenu` (iterator) - The footer menu data
 * - `hasFeedback` (bool) - If there is feedback to display or not
 * - `feedback` (iterator) - The feedback data
 */
class AdminTemplate extends AbstractTemplate
{

    /**
     * Admin configuration
     * from main config['admin']
     * @var array
     */
    protected $adminConfig;

    /**
     * @var string $ident
     */
    private $ident;
    /**
     * @var TranslationString $label
     */
    private $label;

    /**
     * @var TranslationString $title
     */
    private $title;
    /**
     * @var TranslationString $subtitle
     */
    private $subtitle;

    /**
     * @var boolean $showHeaderMenu
     */
    private $showHeaderMenu = true;
    /**
     * @var boolean $showFooterMenu
     */
    private $showFooterMenu = true;

    /**
     * @var array $feedbacks
     */
    private $feedbacks;

    private $app;

    protected function app()
    {
        if ($this->app === null) {
            $this->app = \Charcoal\App\App::instance();
        }
        return $this->app;
    }

    /**
     * Constructor.
     * Ensure authentication before serving the template.
     * @todo Check permissions
     *
     * @param arrray $data
     */
    public function __construct(array $data = null)
    {
        if (!session_id()) {
            session_cache_limiter(false);
            session_start();
        }

        parent::setData($data);

        if ($this->authRequired() !== false) {
            $this->auth();
        }

        // Initialize data with GET
        $this->setData($_GET);
    }

    /**
     * Dependencies
     * @param Container $container
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $baseConfig = $container['config'];
        $this->adminConfig = $baseConfig['admin'];
    }

    /**
     * @param mixed $ident
     * @return AdminTemplate Chainable
     */
    public function setIdent($ident)
    {
        $this->ident = $ident;
        return $this;
    }

    /**
     * @param string
     */
    public function ident()
    {
        return $this->ident;
    }

    /**
     * @param mixed $label
     * @return AdminTemplate Chainable
     */
    public function setLabel($label)
    {
        $this->label = new TranslationString($label);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * @param mixed $title
     * @return AdminTemplate Chainable
     */
    public function setTitle($title)
    {
        $this->title = new TranslationString($title);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function title()
    {
        if ($this->title === null) {
            $this->title = 'Undefined Title';
        }
        return $this->title;
    }

    /**
     * @param mixed $subtitle
     * @return AdminTemplate Chainable
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = new TranslationString($subtitle);
        return $this;
    }

    /**
     * @return TranslationString
     */
    public function subtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param boolean $show
     * @return AdminTemplate Chainable
     */
    public function setShowHeaderMenu($show)
    {
        $this->showHeaderMenu = !!$show;
        return $this;
    }

    /**
     * @return boolean
     */
    public function showHeaderMenu()
    {
        return $this->showHeaderMenu;
    }

    /**
     * @throws Exception If the menu was not properly configured.
     * @return array
     */
    public function headerMenu()
    {
        $headerMenu = $this->adminConfig['header_menu'];
        if (!isset($headerMenu['items'])) {
            throw new Exception(
                'Header menu was not property configured.'
            );
        }
        foreach ($headerMenu['items'] as $menuItem) {
            if ($menuItem['url'] != '#') {
                $menuItem['url'] = $this->adminUrl().$menuItem['url'];
            }
            yield $menuItem;
        }

    }

    /**
     * @param boolean $show
     * @throws InvalidArgumentException
     * @return AdminTemplate Chainable
     */
    public function setShowFooterMenu($show)
    {
        $this->showFooterMenu = !!$show;
        return $this;
    }

    /**
     * @return boolean
     */
    public function showFooterMenu()
    {
        return $this->showFooterMenu;
    }

    /**
     * @return array
     */
    public function footerMenu()
    {
        // @todo
        return [];
    }

    public function token()
    {
        throw new Exception(
            'Function not implemented.'
        );
    }

    /**
     * @return boolean
     */
    public function hasFeedbacks()
    {
        return (count($this->feedbacks()) > 0);
    }

    /**
     * @return array
     */
    public function feedbacks()
    {
        return $this->feedbacks;
    }

    /**
     * @param string $level
     * @param mixed  $msg
     * @return AdminTemplate Chainable
     */
    public function addFeedback($level, $msg)
    {
        $this->feedbacks[] = [
            'msg'=>$msg,
            'level'=>$level
        ];
        return $this;
    }

    /**
     * Determine if user authentication is required.
     *
     * Authentication is required by default. If unnecessary,
     * replace this method in the inherited template class.
     *
     * For example, the "Login" / "Reset Password" templates
     * should return `FALSE`.
     *
     * @return boolean
     */
    protected function authRequired()
    {
        return true;
    }

    /**
     * Determine if the current user is authenticated. If not it redirects them to the login page.
     */
    private function auth()
    {
        $bySession = $this->authBySession();
        if ($bySession === true) {
            return;
        }

        $byToken = $this->authByToken();
        if ($byToken === true) {
            return;
        }

        // Not authenticated. Die.
        header('Location: '.$this->adminUrl().'login');
        exit;
    }

    private function authBySession()
    {
        $u = User::getAuthenticated();
        if ($u && $u->id()) {
            return true;
        } else {
            return false;
        }
    }

    private function authByToken()
    {
        $authToken = new AuthToken([
            'logger' => $this->logger
        ]);

        if ($authToken->metadata()->enabled() !== true) {
            return false;
        }

        $authCookie = $_COOKIE[$authToken->metadata()->cookieName()];
        $vals = explode(';', $authCookie);
        $username =$authToken->getUserId($vals[0], $vals[1]);
        if (!$username) {
            return false;
        }

        $u = new User([
            'logger' => $this->logger
        ]);
        $u->load($username);
        if ($u->id()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function adminUrl()
    {
        $adminPath = $this->app()->getContainer()->get('charcoal/admin/config')->basePath();

        return rtrim($this->baseUrl(), '/').'/'.rtrim($adminPath, '/').'/';
    }

    /**
     * @return string
     */
    public function baseUrl()
    {
        $appConfig = $this->app()->config();

        if ($appConfig->has('URL')) {
            return $appConfig->get('URL');
        } else {
            $uri = $this->app()->getContainer()->get('request')->getUri();

            return rtrim($uri->getBaseUrl(), '/').'/';
        }
    }

    public function forLoop()
    {
        $return = [];

        for ($i = 1; $i <= 10; $i++) {
            $return[$i] = new ArrayIterator(array_combine(range(1, $i), range(1, $i)));
        }

        return $return;
    }


    public function headerMenuLogo()
    {
        if (!isset($this->adminConfig['menu_logo'])) {
            return 'assets/admin/images/user_01.jpg';
        }

        if (!is_string($this->adminConfig['menu_logo'])) {
            return 'assets/admin/images/user_01.jpg';
        }

        return $this->adminConfig['menu_logo'];
    }
}
