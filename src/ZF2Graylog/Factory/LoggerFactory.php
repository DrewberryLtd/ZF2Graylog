<?php
namespace ZF2Graylog\Factory;

use Gelf\Transport\UdpTransport;
use Gelf\Transport\TcpTransport;
use Zend\Log\Exception\RuntimeException;
use Zend\Log\Logger;
use ZF2Graylog\Log\Writer\Graylog2;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $container
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $container)
    {
        $config = $container->get('Config');
        if (!isset($config['graylog']['host']) || !isset($config['graylog']['port'])) {
            throw new RuntimeException('Graylog config values have not been set');
        }

        $host = $config['graylog']['host'];
        $port = $config['graylog']['port'];
        $protocol = $config['graylog']['protocol'];
        $facility = 'ZF 2 Graylog logger';
        if (isset($config['graylog']['facility'])) {
            $facility = $config['graylog']['facility'];
        }
        if (!$host) {
            throw new RuntimeException('Не задан хост логгера Graylog');
        }

        return $this->getGraylogLogger($host, $port, $facility, $protocol);
    }


    /**
     * @return Logger
     */
    private function getGraylogLogger($hostname, $port, $facility, $protocol)
    {
        $logger = new Logger();
        if ('TCP' == $protocol) {
            $transport = new TcpTransport($hostname, $port);
        }
        else {
            $transport = new UdpTransport($hostname, $port);
        }

        $writer = new Graylog2($facility, $transport);
        $logger->addWriter($writer);

        return $logger;
    }
}
