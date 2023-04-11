<?php

namespace Utopia\Tests\Adapter;

use PDO;
use Redis;
use Utopia\Database\Database;
use Utopia\Database\Adapter\OracleDB;
use Utopia\Cache\Cache;
use Utopia\Cache\Adapter\Redis as RedisAdapter;
use Utopia\Tests\Base;

class OracleDBTest extends Base
{
    public static ?Database $database = null;

    // TODO@kodumbeats hacky way to identify adapters for tests
    // Remove once all methods are implemented
    /**
     * Return name of adapter
     *
     * @return string
     */
    public static function getAdapterName(): string
    {
        return "oracledb";
    }

    /**
     * @return Database
     */
    public static function getDatabase(): Database
    {
        if (!is_null(self::$database)) {
            return self::$database;
        }

        $dbHost = 'oracledb';
        $dbPort = '1521';
        $dbUser = 'root';
        $dbPass = 'password';
        $dbName = 'orclpdb1';
        $pdo = oci_connect($dbUser, $dbPass, "//{$dbHost}:{$dbPort}/{$dbName}", 'AL32UTF8');
        $redis = new Redis();
        $redis->connect('redis', 6379);
        $redis->flushAll();
        $cache = new Cache(new RedisAdapter($redis));

        $database = new Database(new OracleDB($pdo), $cache);
        $database->setDefaultDatabase('utopiaTests');
        $database->setNamespace('myapp_'.uniqid());

        return self::$database = $database;
    }
}
