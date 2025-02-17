<?php

if (!function_exists('api_request')) {
    function api_request($url, $method = 'GET', $data = [], $headers = [])
    {
        $client = \Config\Services::curlrequest();

        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $headers = array_merge($defaultHeaders, $headers);

        try {
            $response = $client->request($method, $url, [
                'json' => $data,
                'headers' => $headers
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            log_message('error', 'API Request Error: ' . $e->getMessage());
            return null;
        }
    }
}
