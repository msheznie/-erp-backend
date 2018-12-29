@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template Columns
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'reportTemplateColumns.store']) !!}

                        @include('report_template_columns.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
