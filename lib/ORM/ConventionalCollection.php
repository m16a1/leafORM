<?php

namespace ORM;

trait ConventionalCollection
{
    use \ORM\Conventional;

    public static function getDbCollectionName()
    {
        $parts = explode('\\', get_called_class());
        return self::camelCaseToSnakeCase(end($parts));
    }

    public static function getCollectionName()
    {
        return get_called_class();
    }

    public static function getModelName()
    {
        return rtrim(preg_replace('!\\Collections\\\!', '\Models\\', get_called_class()), 's');
    }

    public static function getEntityName()
    {
        $parts = explode('\\', get_called_class());
        return rtrim(self::camelCaseToSnakeCase(end($parts)), 's');
    }
}