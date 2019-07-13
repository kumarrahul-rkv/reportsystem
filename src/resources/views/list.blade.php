@extends('reportsystem::app')
@section('content')
    @if(isset($report))
        <h3>Edit : </h3>
        {!! Form::model($report, ['route' => ['report.update', $report->id], 'method' => 'patch']) !!}
    @else
        <h3>Add New report : </h3>
        {!! Form::open(['route' => 'report.store']) !!}
    @endif
        <div class="form-inline">
            <div class="form-group">
                {!! Form::text('name',null,['class' => 'form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::submit($submit, ['class' => 'btn btn-primary form-control']) !!}
            </div>
        </div>
    {!! Form::close() !!}
    <hr>
    <h4>reports To Do : </h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
                <tr>
                    <td>{{ $report->name }}</td>
                    <td>
                        {!! Form::open(['route' => ['report.destroy', $report->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="{!! route('report.edit', [$report->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                                {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                            </div>
                        {!! Form::close() !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection