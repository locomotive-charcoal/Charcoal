Charcoal Queue
==============

Queue Managers, Queue Items and Queueable objects (through Interface & Trait) for Charcoal.

## How to install

```
composer require locomotivemtl/charcoal-queue`
```

## Dependencies

-   `locomotivemtl/charcoal-core` for the `CollectionLoader`
-   `locomotivemtl/charcoal-factory` for the queue-item factory.

## Queueing System

Queue managers loop queue items. Queue items represent actions to be performed (as defined by the `process()` method).

## Queue Manager

The queue manager is available as an abstract class: `AbstractQueueManager`.
This class implements the `QueueManagerInterface`.

The processing speed (throttle) can be controlled via the `rate` property, in items per second.

The batch limit (number of items to process per iteration) can be controlled with the `limit` property.

The queue can be identified with the `queue_id`. It can be set with `setQueueId()`.

The queue can be processed with `processQueue()`.
If for any reason the items need to be loaded, it can be done with `loadQueueItems()`.

There are 4 callbacks that can be defined:

-   `setProcessedCallback()`
-   `setItemCallback()`
-   `setItemSuccessCallbak()`
-   `setItemFailureCallback()`

There are only 1 abstract method:

-   `queueItemProto()` which must returns a `QueueItemInterface` instance

## Queue Items

Queue Items should implement the `QueueItemInterface`. This can be helped via the `QueueItemTrait`.

Queue items can be identified with a `queue_id`. (The same `queue_id` used by the queue manager).

Items can be processed with `process($callback, $successCallback, $failureCallback)`.

The queue item properties are:

-   `queue_id`
-   `queue_item_data`
-   `queued_date`
-   `processing_date`
-   `processed_date`
-   `processed`

## Queuable Objects

The `QueueableInterface` defines objects that can be queued. This interface is really simple and only provides:

-   `setQueueId()` which can be inherited from `QueueableTrait`
-   `queueId()` (`queue_id` getter) which can be inherited from `QueueableTrait`
-   `queue($ts = null)` which is abstract and must be written inside class which implement the queueable interface

# Development

To install the development environment:

```shell
$ composer install --prefer-source
```

Run tests with

```shell
$ composer test
```

## API documentation

-   The auto-generated `phpDocumentor` API documentation is available at [https://locomotivemtl.github.io/charcoal-queue/docs/master/](https://locomotivemtl.github.io/charcoal-queue/docs/master/)
-   The auto-generated `apigen` API documentation is available at [https://codedoc.pub/locomotivemtl/charcoal-queue/master/](https://codedoc.pub/locomotivemtl/charcoal-queue/master/index.html)

## Development dependencies

-   `phpunit/phpunit`
-   `squizlabs/php_codesniffer`
-   `php-coveralls/php-coveralls`

## Continuous Integration

| Service | Badge | Description |
| ------- | ----- | ----------- |
| [Travis](https://travis-ci.org/locomotivemtl/charcoal-queue) | [![Build Status](https://travis-ci.org/locomotivemtl/charcoal-queue.svg?branch=master)](https://travis-ci.org/locomotivemtl/charcoal-queue) | Runs code sniff check and unit tests. Auto-generates API documentation. |
| [Scrutinizer](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-queue/) | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-queue/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/locomotivemtl/charcoal-queue/?branch=master) | Code quality checker. Also validates API documentation quality. |
| [Coveralls](https://coveralls.io/github/locomotivemtl/charcoal-queue) | [![Coverage Status](https://coveralls.io/repos/github/locomotivemtl/charcoal-queue/badge.svg?branch=master)](https://coveralls.io/github/locomotivemtl/charcoal-queue?branch=master) | Unit Tests code coverage. |
| [Sensiolabs](https://insight.sensiolabs.com/projects/54cd0da0-455a-479e-81e2-7e5be346b6fd) | [![SensioLabsInsight](https://insight.sensiolabs.com/projects/5c21a1cf-9b21-41c8-82a8-90fbad808a20/mini.png)](https://insight.sensiolabs.com/projects/54cd0da0-455a-479e-81e2-7e5be346b6fd) | Another code quality checker, focused on PHP. |

## Coding Style

The Charcoal-Validator module follows the Charcoal coding-style:

-   [_PSR-1_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
-   [_PSR-2_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
-   [_PSR-4_](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md), autoloading is therefore provided by _Composer_.
-   [_phpDocumentor_](http://phpdoc.org/) comments.
-   Read the [phpcs.xml](phpcs.xml) file for all the details on code style.

> Coding style validation / enforcement can be performed with `composer phpcs`. An auto-fixer is also available with `composer phpcbf`.

## Authors

-   Mathieu Ducharme <mat@locomotive.ca>

# License

**The MIT License (MIT)**

_Copyright © 2016 Locomotive inc._
> See [Authors](#authors).

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
