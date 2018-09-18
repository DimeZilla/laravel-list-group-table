<?php

namespace DiamondLGTAble\Services;

class Tablize
{
    public function columnTitles($columns = [])
    {
        $column_labels = [];
        foreach ($columns as $col) {
            $column_labels[] = $this->colTitle($col);
        }

        return $column_labels;
    }

    public function colValue($col, $row)
    {
        $key = $col['key'] ?? '';
        $cb = isset($col['cb']) && is_callable($col['cb']) ? $col['cb'] : false;
        $value = $col['before'] ?? '';
        $value .= $cb !== false ? $cb($row) : _($row->$key);
        $value .= $col['after'] ?? '';

        return $value;
    }

    public function colTitle($col)
    {
        if (empty($col['title'])) {
            return labelize_db_field($col['key'] ?? '');
        }

        if (is_string($col['title'])) {
            return _($col['title']);
        }

        if (is_callable($col['title'])) {
            return $col['title']();
        }

        return '';
    }

     public function processData($lgdata, $columns, $stripTags = false)
    {
        $data = [];
        // append our column titles
        $data[] = $this->columnTitles($columns);

        if (!empty($lgdata)) {
            foreach ($lgdata as $row) {
                $tmp = [];
                foreach ($columns as $col) {
                    $value = $this->colValue($col, $row);
                    if ($stripTags) {
                        $tmp[] = strip_tags($value);
                    }
                    else {
                        $tmp[] = $value;
                    }
                }
                $data[] = $tmp;
            }
        }

        return $data;
    }
}
