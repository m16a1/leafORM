<?php

namespace ORM\Model;

use ORM\Relation;

trait Relationable
{
    protected static $relations = [];

    protected function hasOne($model, array $options = [])
    {
        $className = $this->getNamespace() . '\Models\\' . $model;
        static::$relations[$className::getModelName()] = new Relation($className::getCollectionName(),
            Relation::HAS_ONE, $options);
    }

    protected function belongsTo($model, array $options = [])
    {
        $className = $this->getNamespace() . '\Models\\' . $model;
        static::$relations[$className::getModelName()] = new Relation($className::getCollectionName(),
            Relation::BELONGS_TO, $options);
    }

    protected function hasMany($collection, array $options = [])
    {
        $className = $this->getNamespace() . '\Collections\\' . $collection;
        static::$relations[$className::getCollectionName()] = new Relation($className, Relation::HAS_MANY, $options);
    }

    protected function hasAndBelongsToMany($collection, array $options = [])
    {
        $className = $this->getNamespace() . '\Collections\\' . $collection;
        static::$relations[$className::getCollectionName()] = new Relation(
            $className, Relation::HAS_AND_BELONGS_TO_MANY, $options);
    }

    public function getByRelation($name)
    {
        return static::$relations[$name]->take($this);
    }

    public function setByRelation($name, \ORM\Model $value)
    {
        $relation = static::$relations[$name];
        $relation->establish($this, $value);
    }

    private function getNamespace()
    {
        return preg_replace('!(Collections|Models)(.+)!', '', get_called_class());
    }
}