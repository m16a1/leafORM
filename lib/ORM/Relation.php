<?php

namespace ORM;

class Relation
{
    const HAS_ONE = 1;
    const BELONGS_TO = 2;
    const HAS_MANY = 3;
    const HAS_AND_BELONGS_TO_MANY = 4;

    private $collection;
    private $type;

    public function __construct($collection, $type, array $options = [])
    {
        $this->collection = $collection;
        $this->type = $type;
    }

    public function take(Model $relatedModel)
    {
        switch ($this->type) {
            case self::HAS_ONE:
                return $this->takeByHasOne($relatedModel);
            case self::BELONGS_TO:
                return $this->takeByBelongsTo($relatedModel);
            case self::HAS_MANY:
                return $this->takeByHasMany($relatedModel);
            case self::HAS_AND_BELONGS_TO_MANY:
                return $this->takeByHasAndBelongsToMany($relatedModel);
        }
        throw new Exception\UnkonwnRelationType();
    }

    private function takeByHasOne(Model $relatedModel)
    {
        return $this->takeByHasMany($relatedModel)->limit(1)->first();
    }

    private function takeByHasMany(Model $relatedModel)
    {
        $collection = $this->collection;
        $id = $relatedModel->get('_id');
        $relatedField = $collection::getEntityName() . '_id';
        return $collection::where([$relatedField => $id]);
    }

    private function takeByBelongsTo(Model $relatedModel)
    {
        $collection = $this->collection;
        $id = $relatedModel->get($relatedModel::getEntityName() . '_id');
        return $collection::where(['_id' => $id])->limit(1)->first();
    }

    private function takeByHasAndBelongsToMany(Model $relatedModel)
    {
        $collection = $this->collection;
        $id = $relatedModel->get($relatedModel::getEntityName() . '_ids');
        return $collection::where(['_id' => ['$in' => $id]]);
    }
}