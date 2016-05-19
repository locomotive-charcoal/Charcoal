<?php

namespace Charcoal\Email\Script;

// PSR-7 (http messaging) dependencies
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

use \Pimple\Container;

// Module `charcoal-app` dependencies
use \Charcoal\App\Script\AbstractScript;
use \Charcoal\App\Script\CronScriptInterface;
use \Charcoal\App\Script\CronScriptTrait;

// Local dependencies
use \Charcoal\Email\EmailQueueManager;

/**
 * Process Email Queue script.
 *
 * Can also be used as a cron script.
 */
class ProcessQueueScript extends AbstractScript implements CronScriptInterface
{
    use CronScriptTrait;

    /**
     * @var FactoryInterface $queueItemFactory
     */
    protected $queueItemFactory;

    /**
     * A copy of all sent message.
     *
     * @var array $sent
     */
    private $sent;

    /**
     * @param Container $container Pimple DI container.
     * @return void
     */
    public function setDependencies(Container $container)
    {
        parent::setDependencies($container);
        $this->queueItemFactory = $container['model/factory'];
    }

    /**
     * Process all messages currently in queue.
     *
     * @param  RequestInterface  $request  A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function run(RequestInterface $request, ResponseInterface $response)
    {
        // Unused parameter
        unset($request);

        $this->startLock();

        $climate = $this->climate();

        $processedCallback = function($success, $failures, $skipped) use ($climate) {
            if (!empty($success)) {
                $climate->green()->out(sprintf('%s emails were successfully sent.', count($success)));
            }

            if (!empty($failures)) {
                $climate->red()->out(sprintf('%s emails were not successfully sent', count($failures)));
            }

            if (!empty($skipped)) {
                $climate->orange()->out(sprintf('%s emails were skipped.', count($skipped)));
            }
        };

        $queueManager = new EmailQueueManager([
            'logger' => $this->logger,
            'queue_item_factory' => $this->queueItemFactory
        ]);
        $queueManager->setProcessedCallback($processedCallback);
        $queueManager->processQueue();

        $this->stopLock();

        return $response;
    }
}
