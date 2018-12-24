@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'reportTemplates.store']) !!}

                        @include('report_templates.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
