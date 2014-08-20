<?php

namespace ORM;

abstract class Collection
{
    use Conventional;

    protected static $isModel = false;
    protected static $collection;

    protected static function getDbCollection()
    {
        if (!static::$collection) {
            $model = static::getModelName();
            static::$collection = $model::getDbCollection();
        }
        return static::$collection;
    }

    /**
     * @param array $searchCriteria
     * @return Query
     */
    public static function where(array $searchCriteria)
    {
        return new Query(static::getDbCollection()->find($searchCriteria), static::getModelName());
    }

    /**
     * @param MongoId $id
     * @return Model
     * @throws DocumentNotFound
     */
    public static function find($id)
    {
        $result = static::getCollection()->findOne(['_id' => $id]);
        if (!$result) {
            throw new DocumentNotFound();
        }
        $model = static::getModelName();
        return new $model($result);
    }

    /**
     * @param array $searchCriteria
     * @return Query
     * @throws Exception\DocumentNotFound
     */
    public static function findBy(array $searchCriteria)
    {
        $result = static::where($searchCriteria);
        if (!$result) {
            throw new Exception\DocumentNotFound();
        }
        return $result;
    }

    /**
     * @param array $criteria
     * @return Query
     */
    public static function findOrCreate(array $criteria)
    {
        return new Query(
            static::getCollection()->findAndModify($criteria, $criteria, ['new' => true]),
            static::getModelName()
        );
    }

    /**
     * @param array $criteria
     * @param array $updateData
     * @return Query
     */
    public static function findAndUpdate(array $criteria, array $updateData)
    {
        return new Query(
            static::getCollection()->findAndModify($criteria, $updateData, ['update' => true, 'upsert' => true]),
            static::getModelName()
        );
    }

    /**
     * @param array $criteria
     * @return bool
     */
    public static function exists(array $criteria)
    {
        return (bool) static::where($criteria)->pluck('_id')->limit(1)->first();

    }
}