<?php

namespace ORM;

class ConnectionManager
{
    private static $connections = [];

    public static function create(array $conf)
    {
        foreach ($conf as $name=>$value) {
            $client = new \MongoClient($value['server'], $value['options']);
            self::$connections[$name] = $client->selectDB($value['database']);
        }
    }

    public static function getConnection($name)
    {
        if (!isset(self::$connections[$name])) {
            throw new Exception\ConnectionNotFound("connection $name not found");
        }
        return self::$connections[$name];
    }
}