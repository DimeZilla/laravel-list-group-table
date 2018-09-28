<?php

namespace DiamondLGTAble\Services;

use DiamondLGTAble\Models\Data;

class Tablize
{
     public function csvData(Data $lgdata, $stripTags = false)
    {
        $data = [];
        // append our column titles

        $data[] = $lgdata->columnTitles();

        if (!$lgdata->data()->isEmpty()) {
            foreach ($lgdata->data() as $row) {
                $tmp = [];
                foreach ($lgdata->columns() as $col) {
                    $value = $col->getDisplayValueForRow($row);
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

    /**
     * [daterize description]
     * @param  [type] $data    [description]
     * @param  [type] $columns [description]
     * @return [type]          [description]
     */
    public function daterize($data = null, $columns = [])
    {
        return new Data($data, $columns);
    }
}
