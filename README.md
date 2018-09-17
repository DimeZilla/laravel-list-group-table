# Laravel List Group Table
A Laravel package for seemlessly creating table like views using Bootstrap 4s List Groups.

## installation
Use composer!
```
composer require dimezilla/laravel-bootstrap-list-group-table
```

## Usage
This package provides a view called `list-group-table`. This view takes a bunch of options that make it easy to generate list group tables:

### Options:
- $data
    - type: A laravel collection
    - description: A collection of data to pass to the list group table
    - default: false
- $header
    - type: Boolean
    - description: Whether or not to display the header
    - default: true
- $columns
    - type: array
    - description: An associative array of rules for how to display each column - see example
    - default: empty array
- $break_size
    - type: string
    - description: when to break the table into something mobile friendly. Uses the bootstrap break points by appending the break to `col-`. i.e. sm, md, lg, xl
    - default: md
- $row_attributes
    - type: array
    - description: An associative array of other attributes to add to each row. see example
    - default: empty array
- $row_clickable
    - type: boolean
    - description: Whether or not to add hover affects to the row. You'll need to generate the actual event outside the view. For instance, I've used row clickable and row_attributes to trigger a bootstrap modal.
    - default: false
- $no_data_text
    - type: callable|string
    - description: You can define a callback or use a string to decide what to do when the table is empty. For instance, you can display "No Data"
    - default: "No Data"
- $exportable
    - type: boolean
    - description: exports the table to csv using a package defined route
    - default: false

### Example:
```
<!-- view.blade.php -->
@php
    $data = collect([
        [
            'name' => 'Josh',
            'age' => 31,
            'height' => '5\'8"',
            // yeah obviously fake numbers
            'wealth' => 68999000000,
            'income' => 350000000
        ]
    ]);
@endphp

@include('lgtable::list-group-table', [
    'data' => $data,
    'row_clickable' => true,
    'exportable' => true,
    'header' => true,
    'columns' => [
        [
            // tells the view where in the row the data is to display
            'key' => 'name',
            // if empty, it will just use ucwords on key
            'title' => 'Employee Name',
            // integer, tells the table how large the column should be, i.e. col-md-2
            'size' => 2,
            // if sort key is present, this column will be sortable by the data key so that the user can click on the column header
            'sortKey' => 'name'
        ],
        [
            'key' => 'age',
            'size' => 1
        ],
        [
            'key' => 'height',
            'size' => 1
        ],
        [
            'title' => 'Income',
            'size' => 2,
            // instead of passing a key, you can pass a callback. the whole data row will get passed to the callback. A cb key takes precedence over the 'key' key so if you pass both, the cb will be used.
            'cb' => function ($row) {
                return $row->income;
            },
            // you can also pass a before key to put something static before the key is called on the row.
            'before' => '<span class="prepend">$',
            // you can also pass an after key to put something static after the key is called on the row.
            'after' => '</span>'
        ]
    ]
]);
```

