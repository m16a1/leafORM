<?php

namespace ORM\Model;

use ORM\Relation;

class Relationable
{
    private static $relations = [];

    protected function hasOne($collection, array $options = [])
    {
        static::$relations[$collection::getModelName()] = new Relation($collection, Relation::HAS_ONE, $options);
    }

    protected function belongsTo($collection, array $options = [])
    {
        static::$relations[$collection::getModelName()] = new Relation($collection, Relation::BELONGS_TO, $options);
    }

    protected function hasMany($collection, array $options = [])
    {
        static::$relations[$collection::getCollectionName()] = new Relation($collection, Relation::HAS_MANY, $options);
    }

    protected function hasAndBelongsToMany($collection, array $options = [])
    {
        static::$relations[$collection::getCollectionName()] = new Relation(
            $collection, Relation::HAS_AND_BELONGS_TO_MANY, $options);
    }

    public function getByRelation($name)
    {
        return static::$relations[$name]->take($this);
    }
}