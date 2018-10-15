<?php

namespace ZF2Graylog\Log\Writer;

use Gelf\MessageValidator;
use Gelf\Publisher;
use Gelf\Transport\AbstractTransport;
use \Zend\Log\Writer\AbstractWriter;
use ZF2Graylog\Log\Formatter\Gelf;

class Graylog2 extends AbstractWriter
{
    /**
     * @var Publisher
     */
    private $publisher;

    /**
     * @var \ZF2Graylog\Log\Formatter\Gelf
     */
    protected $formatter;

    public function __construct($options)
    {
        $facility = $options['facility'] ?? null;
        $transport = $options['transport'] ?? null;
        $messageValidator = new MessageValidator();

        $this->setPublisher(new Publisher($transport, $messageValidator));
        $this->setFormatter(new Gelf($facility));
    }

    public function setFormatter($formatter, array $options = null)
    {
        if (!($formatter instanceof Gelf)) {
            throw new \RuntimeException('Wrong formatter for graylog logger');
        }
        $this->formatter = $formatter;
    }

    /**
     * @param Publisher $publisher
     * @return $this
     */
    public function setPublisher(Publisher $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }


    public function doWrite(array $event)
    {
        $message = $this->formatter->format($event);
        $this->publisher->publish($message);
    }
}