<?php

/*
 * This file is part of the FOSHttpCache package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCache\Test;

use FOS\HttpCache\ProxyClient\Symfony;
use FOS\HttpCache\Test\Proxy\SymfonyProxy;

/**
 * Clears the Symfony HttpCache proxy between tests.
 *
 * The webserver with Symfony is to be started with the WebServerListener.
 *
 * You can define constants in your phpunit to control how this test behaves.
 *
 * To define constants in the phpunit file, use this syntax:
 * <php>
 *     <const name="WEB_SERVER_PORT" value="8080" />
 * </php>
 *
 * WEB_SERVER_PORT     port the PHP webserver listens on (required)
 * WEB_SERVER_HOSTNAME hostname where your application can be reached (required)
 *
 * Note that the SymfonyProxy also uses a SYMFONY_CACHE_DIR constant.
 */
trait SymfonyTest
{
    /**
     * @var Symfony
     */
    protected $proxyClient;

    /**
     * @var SymfonyProxy
     */
    protected $proxy;

    /**
     * Clear Symfony HttpCache.
     *
     * @throws \Exception
     */
    protected function setUp()
    {
        $this->getProxy()->clear();
    }

    /**
     * Get server port.
     *
     * @return int
     *
     * @throws \Exception
     */
    protected function getCachingProxyPort()
    {
        if (!defined('WEB_SERVER_PORT')) {
            throw new \Exception('Set WEB_SERVER_PORT in your phpunit.xml');
        }

        return WEB_SERVER_PORT;
    }

    /**
     * Get the hostname where your application can be reached.
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function getHostName()
    {
        if (!defined('WEB_SERVER_HOSTNAME')) {
            throw new \Exception(
                'To use this test, you need to define the WEB_SERVER_HOSTNAME constant in your phpunit.xml'
            );
        }

        return WEB_SERVER_HOSTNAME;
    }

    /**
     * @return SymfonyProxy
     */
    protected function getProxy()
    {
        if (null === $this->proxy) {
            $this->proxy = new SymfonyProxy();
        }

        return $this->proxy;
    }

    /**
     * Get Symfony proxy client.
     *
     * We use a non-default method for PURGE because the built-in PHP webserver
     * does not allow arbitrary HTTP methods.
     * https://github.com/php/php-src/blob/PHP-5.4.1/sapi/cli/php_http_parser.c#L78-L102
     *
     * @return Symfony
     */
    protected function getProxyClient()
    {
        if (null === $this->proxyClient) {
            $this->proxyClient = new Symfony(
                ['http://127.0.0.1:'.$this->getCachingProxyPort()],
                [
                    'base_uri' => $this->getHostName().':'.$this->getCachingProxyPort(),
                    'purge_method' => 'NOTIFY',
                ]
            );
        }

        return $this->proxyClient;
    }
}
