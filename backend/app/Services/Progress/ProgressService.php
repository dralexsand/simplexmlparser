<?php

namespace App\Services\Progress;

use Illuminate\Support\Str;
use Predis\Client;

class ProgressService
{
    public function storeProgress(string $sessionId, int $count, string $prefix)
    {
        $client = new Client([
            'scheme' => 'tcp',
            'host' => 'redis',
            'port' => 6379,
        ]);

        //$client = new Client();

        $uuid = Str::uuid();

        $key = "$prefix$uuid";

        $date = date('Y-m-d H:i:s');

        $data = [
            'sessionId' => $sessionId,
            'count' => $count,
            'uuid' => $uuid,
            'date' => date('Y-m-d H:i:s'),
        ];

        //$data = "sessionId:$sessionId,count:$count,uuid:$uuid;date:$date";

        //$client->lpush($sessionId, $data);
        //$client->lpush($sessionId, serialize($data));
        $client->rpush($sessionId, json_encode($data));
        //$client->set($key, json_encode($data));

        //$retrieveData = $client->get($key);

        return $data;
    }

    /**
     * @return Client
     */
    public function getConnection(): Client
    {
        return $this->connection;
    }

    /**
     * @param Client $connection
     */
    public function setConnection(Client $connection): void
    {
        $this->connection = $connection;
    }
}
