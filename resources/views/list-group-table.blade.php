@php
    // these are the options and their defaults that the view accepts

    $header = $header ?? true; // whether or not to even show the header
    $break_size = $break_size ?? 'md'; // when to break the table for mobile
    $row_clickable = $row_clickable ?? false; // if clickable we'll apply hover affects

    $no_data_text = $no_data_text ?? 'No Data'; // the message when there is no data
    $exportable = $exportable ?? false; // if exportable we'll add the export button
    $row_attributes = $row_attributes ?? false; // array of attributes we'll add to the rows

    $data = LGTAble::daterize($data ?? [], $columns ?? []);
    $columns = $data->columns();

    // process any sorts on the table
    $sort_by = Request::has('sortBy') ? Request::get('sortBy') : '';
    $sort_bits = explode(',', $sort_by);
    $data->sortRowsByRequest($sort_by);
@endphp


<ul class="list-group">
    @if ($header)
        <li class="list-group-item list-group-item-secondary">
            <div class="row">
                @foreach ($columns as $index => $col)
                    <div class="{{ $col->size() ? 'col-md-' . $col->size() : 'col' }} list-group-item-header-col-item">
                        @if ($col->sortable())
                            @php
                                $sort_key = $index;
                                if ($sort_bits[0] == $sort_key && !isset($sort_bits[1])) {
                                    $sort_key .= ',DESC';
                                }
                            @endphp
                            <a href="{{ append_to_current_query(['sortBy' => $sort_key]) }}">
                        @endif
                        <span>
                            <strong>{!! $col->title() !!}</strong>
                        </span>
                        @if($col->sortable())
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </li>
    @endif

    @if ($data->data()->isEmpty())
        <li class="list-group-item font-weight-bold text-center">
            @if(is_callable($no_data_text))
                <span>{!! $no_data_text() !!}</span>
            @elseif(is_string($no_data_text))
                <span>{{ _($no_data_text) }}</span>
            @endif
        </li>
    @else
        @foreach($data->data() as $item)
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
                        <div class="{{ $col->size() ? 'col-' . $break_size . '-' . $col->size() : 'col' }} list-group-item-col-item decorate">
                            <span>{!! $col->getDisplayValueForRow($item) !!}</span>
                        </div>
                    @endforeach
                </div>
            </li>
        @endforeach
    @endif
</ul>

@if ($exportable)
    <div class="mt-2 text-right">
        <form class="d-inline" method="POST" action="{{ route('lgtable-post-export-data') }}">
            @csrf
            <input type="hidden" name="lgtable-data" value="{{ json_encode(LGTAble::csvData($data, true)) }}" />
            <button class="btn btn-success">{{ _('Export to CSV') }}</button>
        </form>
    </div>
@endif
