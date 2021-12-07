<?php
/**
 * @author Lahiru Dilshan
 * @date 2021-10-21
 */

namespace App\Services\Shared;

use GuzzleHttp\Client;
use Throwable;

class SharedService {
    /**
     * call external apis
     * @param $data
     * @return mixed
     * @throws Throwable
     */
    public function fetch($data){
        $config = [
            'body' => [],
            'timeout' => 100,
            'decode_content' => false,
            'headers'=>[
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        throw_unless($data, 'please provide require params');
        throw_unless($data['url'], 'URL must be provide');
        throw_unless($data['method'], 'Please provide valid API Method');

        if(isset($data['data'])) $config['body'] = json_encode($data['data']);
        if(isset($data['timeout'])) $config['timeout'] = $data['timeout'];
        if(isset($data['headers'])) $config['headers'] = $data['headers'];

        $client = new Client();
        $method = strtolower($data['method']);
        $response = $client->{$method}($data['url'], $config);

        return json_decode($response->getBody()->getContents());
    }
}
