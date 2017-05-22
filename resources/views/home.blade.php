@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            @include('layouts.alerts')
            <form action="{{ url('/home') }}" method="get">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Filter
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-5">
                                <div class="form-group">
                                    <input id="start" class="form-control date" name="start" type="text" placeholder="Start Date" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-5">
                                <div class="form-group">
                                    <input id="end" class="form-control date" name="end" type="text" placeholder="End Date" required>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-2">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-search fa-fw"></i> Go</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="panel panel-default">
                <div class="panel-heading">
                    @if (empty($_GET['start']))
                        Accounting for {{ \Carbon\Carbon::now()->subMonth()->format("F") }} to {{ \Carbon\Carbon::now()->format("F Y") }}
                    @else
                        {{ \Carbon\Carbon::parse($_GET['start'])->format("F j, Y") }} to {{ \Carbon\Carbon::parse($_GET['end'])->format("F j, Y") }}
                    @endif
                    <button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#add-entry"><i class="fa fa-plus fa-fw"></i> Add Entry</button>
                        @if (!Request::has('start'))
                            <a href="{{ url('/home/export') }}" target="_blank" class="btn btn-info btn-xs pull-right hidden-xs" style="margin-right: 1.0em;"><i class="fa fa-file-pdf-o fa-fw"></i> Export to PDF</a>
                        @else
                            <a href="{{ url('/home/export?start=' . $_GET['start'] . '&end=' . $_GET['end']) }}" target="_blank" class="btn btn-info btn-xs pull-right hidden-xs" style="margin-right: 1.0em;"><i class="fa fa-file-pdf-o fa-fw"></i> Export to PDF</a>
                        @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="15%" class="text-center">Date</th>
                            <th width="60%">Description</th>
                            <th class="text-center">In</th>
                            <th class="text-center">Out</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if ($entries->count() == 0)
                            <tr>
                                <td colspan="4" class="text-center"><em>Nothing found.</em></td>
                            </tr>
                        @else
                            @php ($total_in = 0)
                            @php ($total_out = 0)
                            @foreach ($entries as $entry)
                                <tr>
                                    <td class="text-center">
                                        <a href="#" class="edit" data-id="{{ $entry->id }}" data-description="{{ $entry->description }}" data-amount="{{ number_format($entry->amount, 2) }}" data-type="{{ $entry->type }}">
                                            {{ \Carbon\Carbon::parse($entry->created_at)->format("F j, Y g:i A") }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="#" class="edit" data-id="{{ $entry->id }}" data-description="{{ $entry->description }}" data-amount="{{ number_format($entry->amount, 2) }}" data-type="{{ $entry->type }}">
                                            {{ $entry->description }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        @if ($entry->type == 'in')
                                            <a href="#" class="edit" data-id="{{ $entry->id }}" data-description="{{ $entry->description }}" data-amount="{{ number_format($entry->amount, 2) }}" data-type="{{ $entry->type }}">
                                                PHP {{ number_format($entry->amount, 2) }}
                                            </a>
                                            @php ($total_in += (double) number_format($entry->amount, 2))
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($entry->type == 'out')
                                            <a href="#" class="edit" data-id="{{ $entry->id }}" data-description="{{ $entry->description }}" data-amount="{{ number_format($entry->amount, 2) }}" data-type="{{ $entry->type }}">
                                                PHP {{ number_format($entry->amount, 2) }}
                                            </a>
                                            @php ($total_out += (double) number_format($entry->amount, 2))
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" class="text-right">Total</td>
                                <td class="text-center">PHP {{ number_format($total_in, 2) }}</td>
                                <td class="text-center">PHP {{ number_format($total_out, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Net Value</td>
                                <td class="text-center">PHP {{ number_format($total_in - $total_out, 2) }}</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add-entry" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form action="{{ url('/home/create') }}" method="post">
            {!! csrf_field() !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus fa-fw"></i> Add Entry</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <span class="input-group-addon">PHP</span>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" class="form-control" name="type" required>
                            <option value="in">In</option>
                            <option value="out">Out</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="edit-entry" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form action="{{ url('/home/edit') }}" method="post">
            {!! csrf_field() !!}
            <input type="hidden" name="id" id="id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-pencil fa-fw"></i> Edit Entry</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-amount">Amount</label>
                        <div class="input-group">
                            <span class="input-group-addon">PHP</span>
                            <input type="number" class="form-control" name="amount" id="edit-amount" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit-description">Description</label>
                        <textarea class="form-control" id="edit-description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-type">Type</label>
                        <select id="edit-type" class="form-control" name="type" required>
                            <option value="in">In</option>
                            <option value="out">Out</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times fa-fw"></i> Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-fw"></i> Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(function () {
            $('#add-entry').on('shown.bs.modal', function () {
                $('#amount').focus();
            });
            $('.edit').click(function (e) {
                e.preventDefault();
                var me = $(this);
                $('#edit-entry').modal();
                $('#edit-amount').val(me.attr('data-amount'));
                $('#edit-description').val(me.attr('data-description'));
                $('#id').val(me.attr('data-id'));
                if (me.attr('data-type') == 'in') {
                    $('#edit-type').val('in');
                } else {
                    $('#edit-type').val('out');
                }
            });
        });
    </script>
@endsection