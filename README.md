# Graylog2 for Zend Framework 2

A `Zend\Log` writer and formatter helps you add Graylog logging to your ZF2
application, using [bzikarsky/gelf-php][gelf-php]: the newer version of deprecated [Graylog2/gelf-php][gelf-php] library.

## Install

```
composer require debugger84/zf2-graylog:dev-master
```

Add values of graylog connection to your config
 
```
    'graylog' => [
        'host' => 'http://graylog.local',
        'port' => 12222,
        'facility' => 'Your project name'
    ],
```

Rewrite factory for Zend Logger interface

```
\Zend\Log\LoggerInterface::class => \ZF2Graylog\Factory\LoggerFactory::class,
```

## Usage

I added a method to a custom base controller:

    class Base extends \Zend\Mvc\Controller\AbstractActionController {
        private $logger;

        protected function getLogger() {
            if (!isset($this->logger)) {
                $this->logger = new \Zend\Log\Logger;

                // __construct($facility, $hostname, $port)
                $writer = new \ZF2Graylog\Log\Writer\Graylog2('ZF2', '127.0.0.1');
                $this->logger->addWriter($writer);
            }
    
            return $this->logger;
        }
    }

Which can then be used as usual:

    $this->getLogger()->info('Informative message');

The formatter should (or will) support error logging (as below) by mapping the following attributes to their respective GELF fields:

    $logger = new \Zend\Log\Logger;
    $writer = new \ZF2Graylog\Log\Writer\Graylog2('ZF2', '127.0.0.1');
    $logger->addWriter($writer);

    Zend\Log\Logger::registerErrorHandler($logger);


    Zend\Log        GELFMessage
    --------------------------------------------------------------------------------
    message         message, full_message, short_message (unless present in $values)
    errno           level
    file            file
    line            line
    context         additional fields (to be tested)


Additional fields that are associated with GELF fields:

*   `full` (message) is mapped to `full_message` (if present, otherwise `message`)
*   `short` (message) is mapped to `short_message` (if present, otherwise `message`)
*   `version` is mapped to `version` (not set if not present)
*   `facility` is mapped to `facility`. A default facility is set in the writer.
*   Additional fields are mapped as additional fields.

## Todo

*   Write a composer.json that includes the [Graylog2/gelf-php][gelf-php] lib.
*   Docblocks.
*   Further testing of different events.
*   Check errno maps to the correct severity level when used as an errorHandler.

## License

TBC, will probably be MIT.

[gelf-php]: https://github.com/Graylog2/gelf-php
