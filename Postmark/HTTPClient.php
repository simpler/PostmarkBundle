<?php

/*
 * This file is part of the MZ\PostMarkBundle
 *
 * (c) Miguel Perez <miguel@miguelpz.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace MZ\PostmarkBundle\Postmark;

use  Buzz\Browser,
     Buzz\Client\Curl,
     Buzz\Util\Url;

/**
 * HTTP client use to send requests to postmark api
 *
 * @author Miguel Perez <miguel@miguelpz.com>
 */
class HTTPClient
{
    /**
     * cURL headers
     *
     * @var array
     */
    protected $httpHeaders;

    /**
     * Postmark api key
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Buzz proxy settings
     *
     * @var array
     */
    protected $proxy;

    /**
     * Constructor
     *
     * @param string $apiKey
     */
    public function __construct($apiKey, $proxy)
    {
        $this->apiKey = $apiKey;
        $this->proxy = $proxy['server']!='' ? $proxy : null;
        $this->httpHeaders['Accept'] = 'application/json';
        $this->httpHeaders['Content-Type'] = 'application/json';
        $this->httpHeaders['X-Postmark-Server-Token'] =  $this->apiKey;
    }

    /**
     * Set Postmark api key
     *
     * @param string $key
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;
    }

    /**
     * Set cURL headers
     *
     * @param string $name
     * @param string $value
     */
    public function setHTTPHeader($name, $value)
    {
        $this->httpHeaders[$name] = $value;
    }

    /**
     * Make request to postmark api
     *
     * @param string URL to post to
     * @param mixed $data
     */
    public function sendRequest($url, $data)
    {
        $curl = new Curl();

        if ($this->proxy) {
            $url = 'http://' . ($this->proxy['authentication']!='' ? $this->proxy['authentication'].'@' : '') . $this->proxy['server'];
            $proxy_server = new Url($url);
            $curl->setProxy($proxy_server);
        }

        $browser = new Browser($curl);
        $response = $browser->post($url, $this->httpHeaders, $data);

        return $response->getContent();
    }
}
