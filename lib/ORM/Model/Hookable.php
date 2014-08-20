<?php

namespace ORM\Model;

trait Hookable
{
    protected function beforeCreate()
    {}

    protected function afterCreate()
    {}

    protected function beforeUpdate()
    {}

    protected function afterUpdate()
    {}

    protected function beforeUpsert()
    {}

    protected function afterUpsert()
    {}

    protected function beforeDelete()
    {}

    protected function afterDelete()
    {}
}