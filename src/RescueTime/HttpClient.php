<?php
/**
 * rescuetime-api-php
 *
 * Copyright (c) Mirko Borivojevic (http://mirkoborivojevic.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 */

namespace RescueTime;

use Guzzle\Http\Client as GuzzleClient;

/**
 * This class handles HTTP communication with RescueTime API
 */
class HttpClient
{

    /**
     * RescueTime API token
     *
     * @var string
     */
    private $apiKey;

    /**
     * URL to rescue time api
     *
     * @var string
     */
    private $apiEndpoint;

    /**
     * API request format - defaulted to json
     *
     * @var string
     */
    private $format;

    /**
     * Constructs HttpClient
     *
     * @param string $apiKey RescueTime API token
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = 'https://www.rescuetime.com';
        $this->format = 'json';
        $this->guzzleClient = null;
    }

    /**
     * Sends request to RescueTime API
     *
     * @return array Decoded json returned by server
     */
    public function request(RequestQueryParameters $params)
    {
        $client = $this->guzzleClient ?: new GuzzleClient($this->apiEndpoint);

        $request = $client->get(
            '/anapi/data',
            ['Accept' => 'application/json'],
            ['query' => ['key' => $this->apiKey, 'format' => $this->format] + $params->toArray()]
        );

        $response = $request->send();

        return $this->handleResponse($response);
    }

    /**
     * Parses RescueTime API response and handles HTTP/API errors
     * @param  String $response Raw API response
     * @return array            Decoded json returned by server
     * @throws \Exception If API server can not be contacted or returns an error
     */
    private function handleResponse($response)
    {
        if (!$response || !$response->isSuccessful()) {
            throw new \Exception("HTTP request failed");
        }

        if ($response->getInfo("size_download") == 0) {
            throw new \Exception("HTTP body empty");
        }

        try {
            $responseJsonArray = json_decode($response->getBody(), true);
        } catch (RuntimeException $e) {
            throw new \Exception("Invalid JSON response: " . $e->getMessage());
        }

        return $responseJsonArray;
    }
}
