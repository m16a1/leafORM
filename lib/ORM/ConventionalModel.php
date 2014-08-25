<?php

namespace ORM;

trait ConventionalModel
{
    use \ORM\Conventional;

    public static function getDbCollectionName()
    {
        $parts = explode('\\', get_called_class());
        $name = self::camelCaseToSnakeCase(end($parts));
        return $name . 's';
    }

    public static function getCollectionName()
    {
        $class = get_called_class();
        return preg_replace('!\\Models\\\!', '\\Collections\\', $class) . 's';
    }

    public static function getModelName()
    {
        return get_called_class();
    }

    public static function getEntityName()
    {
        $parts = explode('\\', get_called_class());
        $name = self::camelCaseToSnakeCase(end($parts));
        return $name;
    }
}