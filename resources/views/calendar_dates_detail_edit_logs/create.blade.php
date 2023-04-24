@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Calendar Dates Detail Edit Log
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'calendarDatesDetailEditLogs.store']) !!}

                        @include('calendar_dates_detail_edit_logs.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
