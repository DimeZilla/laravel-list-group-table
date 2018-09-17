<?php
/**
 * Some routes for our helping
 */

/**
 * Our export data route.
 * Adapted from a stack overflow question
 * @see  https://stackoverflow.com/questions/26146719/use-laravel-to-download-table-as-csv/27596496#27596496
 */
$export = function (Request $request) {
    $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
        ,   'Content-type'        => 'text/csv'
        ,   'Content-Disposition' => 'attachment; filename=galleries.csv'
        ,   'Expires'             => '0'
        ,   'Pragma'              => 'public'
    ];

    $data = [];
    if ($request->has('data')) {
        $data = $request->get('data');
    }

    # add headers for each column in the CSV download
    array_unshift($data, array_keys($data[0]));

   $callback = function() use ($data)
    {
        $FH = fopen('php://output', 'w');
        foreach ($data as $row) {
            fputcsv($FH, $row);
        }
        fclose($FH);
    };

    return Response::stream($callback, 200, $headers);
};

Route::post('export-data', $export)->name('lgtable-post-export-data');
Route::get('export-data', $export)->name('lgtable-get-export-data');
