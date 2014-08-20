<?php

namespace ORM;

trait Conventional
{
    protected static $isModel = true;

    public static function getDbCollectionName()
    {
        $parts = explode('\\', get_called_class());
        $name = self::camelCaseToSnakeCase(end($parts));
        if (self::$isModel) {
            return $name . 's';
        }
        return $name;
    }

    public static function getCollectionName()
    {
        $class = get_called_class();
        if (self::$isModel) {
            return preg_replace('!\\Models\\', '\\Collections\\', $class) . 's';
        }
        return $class;
    }

    public static function getModelName()
    {
        $class = get_called_class();
        if (self::$isModel) {
            return $class;
        }
        return rtrim(preg_replace('!\\Collections\\', '\\Models\\', $class), 's');
    }

    public static function getEntityName()
    {
        $parts = explode('\\', get_called_class());
        $name = self::camelCaseToSnakeCase(end($parts));
        if (self::$isModel) {
            return $name;
        }
        return rtrim($name, 's');

    }

    protected static function camelCaseToSnakeCase($str)
    {
        return ltrim(
                strtolower(
                    preg_replace('!([[:upper:]])!', '_$1', $str)
                )
        , '_');
    }
}