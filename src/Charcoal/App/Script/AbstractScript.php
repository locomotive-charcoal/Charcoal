<?php

namespace Charcoal\App\Script;

// Dependencies from `PHP`
use \InvalidArgumentException;

// PSR-3 (logger) dependencies
use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerAwareTrait;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

// Dependencies from `Pimple`
use \Pimple\Container;

// `thephpleague/climate` dependencies
use \League\CLImate\CLImate;

// Module `charcoal-config` dependencies
use \Charcoal\Config\AbstractEntity;

// Intra-module (`charcoal-app`) dependencies
use \Charcoal\App\AppInterface;
use \Charcoal\App\Script\ScriptInterface;

/**
 *
 */
abstract class AbstractScript extends AbstractEntity implements
    LoggerAwareInterface,
    ScriptInterface
{
    use LoggerAwareTrait;

    /**
     * @var string $ident
     */
    private $ident;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var array $arguments
     */
    private $arguments;

    /**
     * @var CLImate $climate
     */
    private $climate;

    /**
     * @var boolean $quiet
     */
    private $quiet = false;

    /**
     * @var boolean $verbose
     */
    private $verbose = false;

    /**
     * @param array|\ArrayAccess $data The dependencies (app and logger).
     */
    public function __construct($data = null)
    {
        $this->setLogger($data['logger']);
    }

    /**
     * Give an opportunity to children classes to inject dependencies from a Pimple Container.
     *
     * Does nothing by default, reimplement in children classes.
     *
     * The `$container` DI-container (from `Pimple`) should not be saved or passed around, only to be used to
     * inject dependencies (typically via setters).
     *
     * @param Container $container A dependencies container instance.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        // This method is a stub. Reimplement in children template classes.
    }

    /**
     * @return void
     */
    protected function init()
    {
        $arguments = $this->defaultArguments();
        $this->setArguments($arguments);
    }

    /**
     * @param RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    final public function __invoke(RequestInterface $request, ResponseInterface $response)
    {
        $this->init();
        $climate = $this->climate();

        if ($climate->arguments->defined('help')) {
            $climate->usage();
            return $response;
        }

        $climate->arguments->parse();
        $this->setQuiet($climate->arguments->get('quiet'));
        $this->setVerbose($climate->arguments->get('verbose'));

        return $this->run($request, $response);
    }

    /**
     * Safe climate getter.
     * If the instance was not previously set, create it.
     *
     * > CLImate is "PHP's best friend for the terminal."
     * > "CLImate allows you to easily output colored text, special formats, and more."
     *
     * @return CLImate
     */
    protected function climate()
    {
        if ($this->climate === null) {
            $this->climate = new CLImate();
        }
        return $this->climate;
    }

    /**
     * @return array
     */
    public function defaultArguments()
    {
        return [
            'help' => [
                'longPrefix'   => 'help',
                'description'  => 'Prints a usage statement.',
                'noValue'      => true
            ],
            'quiet' => [
                'prefix'       => 'q',
                'longPrefix'   => 'quiet',
                'description'  => 'Disable output as much as possible.',
                'noValue'      => false
            ],
            'verbose' => [
                'prefix'        => 'v',
                'longPrefix'    => 'verbose',
                'description'   => ''
            ]
        ];
    }

    /**
     * @param string $ident The script identifier string.
     * @throws InvalidArgumentException If the ident argument is not a string.
     * @return ScriptInterface Chainable
     */
    public function setIdent($ident)
    {
        if (!is_string($ident)) {
            throw new InvalidArgumentException(
                'Ident must be a string'
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

    /**
     * @param string $description The script description.
     * @throws InvalidArgumentException If the deescription parameter is not a string.
     * @return ScriptInterface Chainable
     */
    public function setDescription($description)
    {
        if (!is_string($description)) {
            throw new InvalidArgumentException(
                'Description must be a string'
            );
        }
        $this->description = $description;
        $this->climate()->description($description);
        return $this;
    }

    /**
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * @param boolean $quiet The quiet flag.
     * @return ScriptInterface Chainable
     */
    public function setQuiet($quiet)
    {
        $this->quiet = !!$quiet;
        return $this;
    }

    /**
     * @return boolean
     */
    public function quiet()
    {
        return $this->quiet;
    }

    /**
     * @param boolean $verbose The verbose flag.
     * @return ScriptInterface Chainable
     */
    public function setVerbose($verbose)
    {
        $this->verbose = !!$verbose;
        return $this;
    }

    /**
     * @return boolean
     */
    public function verbose()
    {
        return $this->verbose;
    }

    /**
     * @param array $arguments The scripts argument array, as [key=>value].
     * @return ScriptInterface Chainable
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = [];
        foreach ($arguments as $argumentIdent => $argument) {
            $this->addArgument($argumentIdent, $argument);
        }

        return $this;
    }

    /**
     * @param string $argumentIdent The argument identifier.
     * @param array  $argument      The argument options.
     * @throws InvalidArgumentException If the argument ident is not a string.
     * @return ScriptInterface Chainable
     */
    public function addArgument($argumentIdent, array $argument)
    {
        if (!is_string($argumentIdent)) {
            throw new InvalidArgumentException(
                'Argument ident must be a string.'
            );
        }
        $this->arguments[$argumentIdent] = $argument;
        $this->climate()->arguments->add([$argumentIdent=>$argument]);
        return $this;
    }

    /**
     * @return array $arguments
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * @param string $argumentIdent The argument identifier to retrieve options from.
     * @return array|null The argument options, or null if it does not exist.
     */
    public function argument($argumentIdent)
    {
        if (!isset($this->arguments[$argumentIdent])) {
            return null;
        }
        return $this->arguments[$argumentIdent];
    }

    /**
     * Get an argument either from argument list (if set) or else from an input prompt.
     *
     * @param string $argName The argument identifier to read from list or input.
     * @return string The argument value or prompt value
     */
    protected function argOrInput($argName)
    {
        $climate = $this->climate();
        $arg = $climate->arguments->get($argName);

        if ($arg) {
            return $arg;
        }

        $arguments = $this->arguments();
        if (isset($arguments[$argName])) {
            $a = $arguments[$argName];
            $arg_desc = $a['description'];
            $input_type = isset($a['inputType']) ? $a['inputType'] : 'text';
            $choices = isset($a['choices']) ? $a['choices'] : null;
        } else {
            $arg_desc = $argName;
            $input_type = 'text';
            $choices = null;
        }

        if ($input_type == 'checkbox') {
            $input = $climate->checkboxes(sprintf('Select %s', $arg_desc), $choices);
        } else {
            $input = $climate->input(sprintf('Enter %s:', $arg_desc));
            if ($choices) {
                $input->accept(array_keys($choices), true);
            }
        }
        $arg = $input->prompt();
        return $arg;
    }
}
