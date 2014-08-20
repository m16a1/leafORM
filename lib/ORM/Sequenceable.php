<?php

namespace ORM;

trait Sequenceable
{
    /**
     * @param callable $func
     * @return static
     */
    public function collect(\Closure $func)
    {
        $result = [];
        foreach ($this->each() as $item) {
            $result[] = $func($item);
        }
        return new static($result);
    }

    /**
     * @param callable $func
     * @return static
     */
    public function map(\Closure $func)
    {
        return $this->collect($func);
    }

    /**
     * @param callable $func
     * @param null $startValue
     * @return static
     */
    public function reduce(\Closure $func, $startValue = null)
    {
        $result = $startValue;
        foreach ($this->each() as $item) {
            $result = $func($result, $item);
        }
        return $result;
    }

    /**
     * @param callable $func
     * @param null $startValue
     * @return static
     */
    public function inject(\Closure $func, $startValue = null)
    {
        return $this->reduce($func, $startValue);
    }

    /**
     * @param callable $func
     * @param null $startValue
     * @return static
     */
    public function foldl(\Closure $func, $startValue = null)
    {
        return $this->reduce($func, $startValue);
    }

    /**
     * @param callable $func
     * @param $startValue
     * @return mixed
     */
    public function reduceRight(\Closure $func, $startValue)
    {
        return $this->reverse()->reduce($func, $startValue);
    }

    /**
     * @param callable $func
     * @param null $startValue
     * @return mixed
     */
    public function foldr(\Closure $func, $startValue = null)
    {
        return $this->reduceRight($func, $startValue);
    }

    /**
     * @return static
     */
    public function reverse()
    {
        return new static(array_reverse($this->toArray()));
    }

    /**
     * @param callable $func
     * @return mixed
     */
    public function detect(\Closure $func)
    {
        foreach ($this->each() as $item) {
            if ($func($item)) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param callable $func
     * @return mixed
     */
    public function find(\Closure $func)
    {
        return $this->detect($func);
    }

    /**
     * @param callable $func
     * @return static
     */
    public function select(\Closure $func)
    {
        $result = [];
        foreach ($this->each() as $item) {
            if ($func($item)) {
                $result[] = $item;
            }
        }
        return new static($result);
    }

    /**
     * @param callable $func
     * @return static
     */
    public function filter(\Closure $func)
    {
        return $this->select($func);
    }

    /**
     * @param callable $func
     * @return static
     */
    public function reject(\Closure $func)
    {
        $result = [];
        foreach ($this->each() as $item) {
            if (!$func($item)) {
                $result[] = $item;
            }
        }
        return new static($result);
    }

    public function first()
    {
        return $this->each()->current();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->each());
    }

    /**
     * Placeholder method
     * @return \Iterator
     */
    public function each()
    {
        return new \ArrayIterator();
    }
}
