@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tender Edit Log Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'tenderEditLogMasters.store']) !!}

                        @include('tender_edit_log_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
