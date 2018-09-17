@php
    // these are the options and their defaults that the view accepts
    $header = $header ?? true;
    $columns = $columns ?? [];
    $data = $data ?? false;
    if ($data !== false && Request::has('sortBy')) {
        $sort_by = Request::get('sortBy');
        if (strpos($sort_by, ',') !== false) {
            $bits = explode(',', $sort_by);
            if ($bits[1] == 'DESC') {
                $data = $data->sortByDesc($bits[0]);
            }
        }
        else {
            $data = $data->sortBy(Request::get('sortBy'));
        }
    }
    $break_size = $break_size ?? 'md';
    $row_attributes = $row_attributes ?? false;
    $row_clickable = $row_clickable ?? false;
    $no_data_text = $no_data_text ?? 'No Data';
    $exportable = $exportable ?? false;
@endphp

@if($data)
    <ul class="list-group">
        @if ($header)
            <li class="list-group-item list-group-item-secondary">
                <div class="row">
                    @foreach ($columns as $index => $col)
                        <div class="{{ $col['size'] ? 'col-md-' . $col['size'] : 'col' }} list-group-item-header-col-item">
                            @if(isset($col['sortKey']))
                                @php
                                    $sort_key = $col['sortKey'];
                                    if (Request::has('sortBy') && Request::get('sortBy') == $sort_key) {
                                        $sort_key .= ',DESC';
                                    }
                                @endphp
                                <a href="{{ append_to_current_query(['sortBy' => $sort_key]) }}">
                            @endif
                            <span>
                                <strong>{!! lgtable_service()->colTitle($col) !!}</strong>
                            </span>
                            @if(isset($col['sortKey']))
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </li>
        @endif

        @if ($data->isEmpty())
            <li class="list-group-item font-weight-bold text-center">
                @if(is_callable($no_data_text))
                    <span>{!! $no_data_text() !!}</span>
                @elseif(is_string($no_data_text))
                    <span>{{ _($no_data_text) }}</span>
                @endif
            </li>
        @else
            @foreach($data as $item)
                @php
                 $rowClasses = '';
                 if (isset($row_attributes['class'])) {
                    if (isset($row_attributes['class'])) {
                        if (is_callable($row_attributes['class'])) {
                            $rowClasses = $row_attributes['class']($item) ?? '';
                        }
                        else if (is_array($row_attributes['class'])) {
                            $rowClasses = join(' ', $row_attributes['class']);
                        }
                        else {
                            $rowClasses = $row_attributes['class'] ?? '';
                        }
                    }
                 }
                @endphp
                <li
                    class="list-group-item {{ $row_clickable ? 'admin-user' : '' }} {{ $rowClasses }}"
                    @if($row_attributes)
                            @foreach($row_attributes as $at => $value)
                                @if (empty($value) || $at == 'class')
                                    @continue
                                @elseif (is_callable($value))
                                    {{ $at }}="{{ $value($item) ?? '' }}"
                                @else ($rowData['type'] == 'static')
                                    {{ $at }}="{{ $value ?? '' }}"
                                @endif
                            @endforeach
                        @endif
                >
                    <div class="row">
                        @foreach ($columns as $col)
                            <div class="{{ $col['size'] ? 'col-' . $break_size . '-' . $col['size'] : 'col' }} list-group-item-col-item decorate">
                                <span>{!! lgtable_service()->colValue($col, $item) !!}</span>
                            </div>
                        @endforeach
                    </div>
                </li>
            @endforeach
        @endif
    </ul>

    @if ($exportable)
        <div class="mt-2 text-right">
            @php
                session()->flash('lgtable-data', lgtable_service()->processData($data, $columns, true));
            @endphp
            <a href="{{ route('lgtable-get-export-data') }}" class="btn btn-success" target="_blank">{{ _('Export to CSV') }}</a>
        </div>
    @endif
@endif
