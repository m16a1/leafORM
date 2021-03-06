<?php

namespace ORM;

use ORM\Exception\NoShardingKeyProvided;
use ORM\Exception\UnknownMethod;
use ORM\Model\Hookable;
use ORM\Model\Relationable;

abstract class Model
{
    use ConventionalModel;
    use Hookable;
    use Relationable;

    private $syncedValues = [];
    private $changedValues = [];
    private $shardingKey = ['_id'];

    public function __construct(array $args, array $syncedValues = [])
    {
        $this->changedValues = $args;
        $this->syncedValues = $syncedValues;
        $this->changedValues['_id'] = new \MongoId();
        if (!static::$relations) {
            $this->setRelations();
        }
    }

    protected function setRelations()
    {}

    public static function getDbCollection()
    {
        return ConnectionManager::getConnection('default')->selectCollection(static::getDbCollectionName());
    }

    public function save()
    {
        if ($this->syncedValues) {
            $this->beforeUpdate();
            $this->syncedValues = static::getDbCollection()->findAndModify($this->getOptimalKey(),
                $this->changedValues, ['update' => true]);
            $this->afterUpdate();
        } else {
            $this->beforeCreate();
            static::getDbCollection()->insert($this->changedValues);
            $this->afterCreate();
            $this->syncedValues = $this->changedValues;
        }
        $this->changedValues = [];
    }

    public function upsert()
    {
        $this->beforeUpsert();
        $this->getDbCollection()->update($this->getOptimalKey(),
            $this->changedValues, ['multiple' => true]);
        $this->afterUpsert();
    }

    public function assign($args)
    {
        foreach ($args as $arg=>$value) {
            if (!isset($this->syncedValues[$arg]) || $this->syncedValues[$arg] != $value) {
                $this->changedValues[$arg] = $value;
            }
        }
    }

    public function reset()
    {
        $this->changedValues = [];
    }

    public function delete()
    {
        $this->beforeDelete();
        if ($this->syncedValues) {
            $this->getDbCollection()->remove($this->getOptimalKey());
        }
        $this->afterDelete();
    }

    public function __call($name, $args)
    {
        if (preg_match('!get(\w+)!', $name, $match)) {
            if (isset(static::$relations[$name])) {
                return $this->getByRelation($name);
            }
            return $this->get(self::camelCaseToSnakeCase($match[1]));
        } elseif (preg_match('!set(\w+)!', $name, $match)) {
            if (isset(static::$relations[$name])) {
                return $this->setByRelation($name, current($args));
            }
            return $this->set(self::camelCaseToSnakeCase($match[1]), current($args));
        }
        throw new UnknownMethod;
    }

    public function get($name)
    {
        if (isset($this->changedValues[$name])) {
            return $this->changedValues[$name];
        }
        if (isset($this->syncedValues[$name])) {
            return $this->syncedValues[$name];
        }
        return null;
    }

    public function set($name, $value)
    {
        $this->changedValues[self::camelCaseToSnakeCase($name)] = $value;
        return $this;
    }

    private function getOptimalKey()
    {
        $result = [];
        foreach ($this->shardingKey as $field) {
            if (!$this->syncedValues[$field]) {
                throw new NoShardingKeyProvided();
            }
            $result[$field] = $this->syncedValues[$field];
        }
        return $result;
    }
}