<?php
// app/Helpers/api_helper.php

/**
 * Make API requests to the backend service
 *
 * @param string $url The API endpoint URL
 * @param string $method HTTP method (GET, POST, PUT, DELETE)
 * @param array $data Request body data for POST/PUT requests
 * @param array $headers Additional headers to include
 * @return array|null Response data or null on failure
 */
function api_request($url, $method = 'GET', $data = [], $headers = [])
{
    $client = \Config\Services::curlrequest();

    $options = [
        'headers' => array_merge([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ], $headers),
        'timeout' => 5,
        'http_errors' => false
    ];

    if ($method === 'POST' || $method === 'PUT') {
        $options['json'] = $data;
    }

    try {
        $response = $client->request($method, $url, $options);
        $body = $response->getBody();

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return json_decode($body, true);
        } else {
            $error = json_decode($body, true) ?? ['message' => 'API request failed'];
            log_message('error', 'API Error: ' . json_encode($error));
            return ['error' => true, 'message' => $error['message'] ?? 'Unknown error'];
        }
    } catch (\Exception $e) {
        log_message('error', 'API Exception: ' . $e->getMessage());
        return ['error' => true, 'message' => 'Could not connect to the server. Please try again later.'];
    }
}
