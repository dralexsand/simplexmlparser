<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Predis\Client;

class TestController extends Controller
{
    public function index()
    {
        $client = new Client();

        $client = new Client([
            'scheme' => 'tcp',
            'host' => 'redis',
            'port' => 6379,
        ]);

        /*$sessionId = Str::uuid();
        $sessionId = "session_{$sessionId}";
        $count = 137;

        $date = date('Y_m_d_h_i_s');
        $i = 7;
        $prefix = "uuid_{$date}_{$i}_";
        $uuid = Str::uuid();
        $key = "$prefix$uuid";

        $data = [
            'sessionId' => $sessionId,
            'count' => $count,
            'uuid' => $uuid,
            'date' => date('Y-m-d H:i:s'),
        ];

        $client->set($key, json_encode($data));
        //$client->set($key, serialize($data));

        //$client->set('key2', 'foo2');

        $data = $client->get('APtIGiZxyaAa0tl2v7XGdH4Bx9wrHOP7');
        $data = $client->get($key);*/

        //$data = $client->getrange('simplexlsparser_database_queues:default', 0, -1);
        //$result = $client->getConnection('simplexlsparser_database_queues:default');
        //$result = $client->getConnection();
        $result = $client->get('uuid_2023_04_03_01_16_20_7_4d7ce6b6-0618-4077-a235-bf02a410fbdb');

        /*$result = [];

        foreach ($data as $item){
            $result[] = unserialize($item);
        }*/

        return $result;
    }
}
