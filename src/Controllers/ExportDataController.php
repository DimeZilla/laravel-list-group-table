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

    /**
     * Storage for our request
     * @var Illuminate\Http\Request
     */
    protected $request;

    /**
     * Our response headers
     * @var array
     */
    protected $headers = [
        'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        'Content-type'        => 'text/csv',
        'Content-Disposition' => 'attachment; filename=data-export.csv',
        'Expires'             => '0',
        'Pragma'              => 'public',
    ];

    /**
     * The actual export data controller.
     * @param  Request $request  the Request object
     * @return Illuminate\Support\Facades\Response
     */
    public function export(Request $request)
    {
        $this->request = $request;

        $data = $this->reqGet('lgtable-data');
        $callback = $this->getCB($data);

        return Response::stream($callback, 200, $this->headers);
    }

    /**
     * From the request or the session, gets a specific key
     * @param  string $key  the key to retrieve
     * @return mixed
     */
    protected function reqGet($key = '')
    {
        if (empty($key) || empty($this->request)) {
            return false;
        }

        try {
            if ($this->request->has($key)) {
                $data = $this->request->get($key);
                if ($this->is_json($data)) {
                    $data = json_decode($data, true);
                }
                return $data;
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

    /**
     * Checks if a variable is json
     * @param  mixed  $string  whatever variable we are testing
     * @return boolean
     */
    private function is_json($string = null)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * sets up our stream callback
     * @param  array  $data  the data we are passing on to create the csv
     * @return callable
     */
    protected function getCB($data = [])
    {
        return function() use ($data)
        {
            $FH = fopen('php://output', 'w');
            foreach ($data as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
            return redirect()->back();
        };
    }
}
