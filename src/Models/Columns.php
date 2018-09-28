<?php

namespace DiamondLGTAble\Models;

use DiamondLGTable\Facades\LGTable;
use Illuminate\Support\Collection;

class Columns extends Collection
{
    public function __construct($cols = [])
    {
        if (!empty($cols)) {
            foreach ($cols as &$col) {
                $col = new Col($col);
            }
        }
        parent::__construct($cols);
    }
}
