<?php

namespace DiamondLGTAble\Models;

use DiamondLGTable\Facades\LGTable;
use JsonSerializable;

class Col implements JsonSerializable
{
    private $col;

    public function __construct($col)
    {
        $this->col = $col;
    }

    public function sortable()
    {
        return $this->col['sortable'] ?? true;
    }

    public function title()
    {
        if (empty($this->col['title'])) {
            return labelize_db_field($this->col['key'] ?? '');
        }

        if (is_string($this->col['title'])) {
            return _($this->col['title']);
        }

        if (is_callable($this->col['title'])) {
            return $this->col['title']();
        }

        return '';
    }

    public function before()
    {
        return $this->col['before'] ?? '';
    }


    public function after()
    {
        return $this->col['after'] ?? '';
    }

    public function isCallBack()
    {
        return isset($this->col['cb'])
            && !is_string($this->col['cb'])
            && is_callable($this->col['cb']);
    }

    public function dataKey()
    {
        return $this->col['key'] ?? '';
    }

    public function callBack()
    {
        return $this->isCallBack() ? $this->col['cb'] : false;
    }

    public function getDisplayValueForRow($row = [])
    {
        $ret = $this->before();
        $ret .= _($this->getValueForRow($row));
        $ret .= $this->after();
        return $ret;
    }

    public function getValueForRow($row = [])
    {
        $key = $this->dataKey();
        $cb = $this->callBack();
        if ($cb !== false ) {
            return $cb($row);
        }
        elseif (!empty($key)) {
            return $row->$key ?? '';
        }

        return null;
    }

    public function size()
    {
        return $this->col['size'] ?? false;
    }

    public function __get($key)
    {
        if (isset($this->col[$key])) {
            return $this->col[$key];
        }

        return null;
    }

    public function __set($key, $value)
    {
        $this->col[$key] = $value;
    }

    public function __call($method, $arguments = [])
    {
        if (isset($this->col[$method])) {
            return $this->col[$method];
        }

        return null;
    }

    public function jsonSerialize()
    {
        return $this->col;
    }
}
