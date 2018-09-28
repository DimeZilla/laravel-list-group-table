<?php

namespace DiamondLGTAble\Models;

use Illuminate\Support\Facade\Request;

class Data
{
    /**
     * Stores our new Columns collection
     * @var \DiamondLGTAble\Models\Columns
     */
    private $columns;

    /**
     * Stores a collection
     * @var \Illuminate\Support\Collection
     */
    private $data;

    public function __construct($data = null, $columns = [])
    {
        $this->columns = new Columns($columns);
        $this->data = collect($data);
    }

    public function columns()
    {
        return $this->columns;
    }

    public function data()
    {
        return $this->data;
    }

    /**
     * gets all of the column titles
     * @return [type] [description]
     */
    public function columnTitles()
    {
        $column_labels = [];
        foreach ($this->columns as $col) {
            $column_labels[] = $col->title();
        }

        return $column_labels;
    }

    /**
     * Processes any sort based on the sortBy request
     * @param  string $sort_by  from the url param sortBy
     * @return void
     */
    public function sortRowsByRequest($sort_by = null)
    {
        if (empty($sort_by))
            return;

        // default our options
        $options = SORT_REGULAR;
        $descending = false;

        if (strpos($sort_by, ',') !== false) {
            $bits = explode(',', $sort_by);
            $sort_by = $bits[0];
            if ($bits[1] == 'DESC') {
                $descending = true;
            }
        }

        // get the column we are sorting by
        $col = $this->columns()->get($sort_by);
        if (!empty($col)) {
            $this->data = $this->data()->sortBy(function ($row, $key) use ($col) {
                $value = $col->getValueForRow($row);
                // if its a string, lets base our comparison on it trimmed and
                // lowercase
                if (is_string($value)) {
                    return strtolower(trim($value));
                }

                return $value;
            }, $options, $descending);
        }
    }
}
