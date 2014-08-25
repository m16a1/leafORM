<?php

namespace ORM;

trait Conventional
{
    protected static function camelCaseToSnakeCase($str)
    {
        return ltrim(
                strtolower(
                    preg_replace('!([[:upper:]])!', '_$1', $str)
                )
        , '_');
    }
}