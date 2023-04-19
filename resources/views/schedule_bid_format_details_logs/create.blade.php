@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Schedule Bid Format Details Log
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'scheduleBidFormatDetailsLogs.store']) !!}

                        @include('schedule_bid_format_details_logs.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
