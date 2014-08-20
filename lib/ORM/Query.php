<?php

namespace ORM;

class Query
{
    use Sequenceable;

    private $cursor;
    private $result;

    public function __construct($dataSource, $model = null)
    {
        if ($dataSource instanceof \MongoCursor) {
            $this->cursor = $dataSource;
            $this->model = $model;
        } elseif (is_array($dataSource)) {
            $this->result = $dataSource;
        }
    }

    public function count()
    {
        return $this->cursor->count();
    }

    public function each()
    {
        if ($this->cursor) {
            $this->cursor->rewind();
            while ($item = $this->cursor->getNext()) {
                yield new $this->model($item);
            }
        } else {
            foreach ($this->result as $v) {
                yield $v;
            }
        }
    }

    public function limit($num)
    {
        $this->cursor->limit($num);
        return $this;
    }

    public function skip($num)
    {
        $this->cursor->skip($num);
        return $this;
    }

    public function setHint(array $hint)
    {
        $this->cursor->hint($hint);
        return $this;
    }

    public function setTimeout($seconds)
    {
        $this->cursor->timeout($seconds * 1000);
        return $this;
    }

    public function pluck()
    {
        $keys = func_get_args();
        $this->cursor->fields(array_combine($keys, array_fill(0, count($keys) - 1, 1)));
        return $this;
    }

    public function orderBy(array $fields)
    {
        $this->cursor->sort($fields);
        return $this;
    }
}