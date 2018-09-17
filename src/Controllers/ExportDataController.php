<?php

namespace DiamondLGTAble\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

/**
 * Our export data class
 * Adapted from a stack overflow answer
 * @see  https://stackoverflow.com/questions/26146719/use-laravel-to-download-table-as-csv/27596496#27596496
 */
class ExportDataController
{

    protected $request;

    protected $headers = [
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Content-type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename=data-export.csv',
        'Expires'             => '0',
        'Pragma'              => 'public',
    ];

    public function export(Request $request)
    {
        $this->request = $request;

        $data = $this->reqGet('lgtable-data');
        $callback = $this->getCB($data);

        return Response::stream($callback, 200, $this->headers);
    }

    protected function reqGet($key = '')
    {
        if (empty($key) || empty($this->request)) {
            return false;
        }

        try {
            if ($this->request->has($key)) {
                return $this->request->get($key);
            }
            else if ($this->request->session()->has($key)) {
                return $this->request->session()->get($key);
            }
            }
        catch (\Throwable $error) {
            // silence - return false on failure
        }

        return false;
    }

    protected function getCB($data = [])
    {
        return function() use ($data)
        {
            $FH = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };
    }
}
