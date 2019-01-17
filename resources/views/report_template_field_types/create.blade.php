@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Report Template Field Type
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'reportTemplateFieldTypes.store']) !!}

                        @include('report_template_field_types.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
