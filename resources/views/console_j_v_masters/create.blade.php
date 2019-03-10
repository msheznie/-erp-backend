@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Console J V Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'consoleJVMasters.store']) !!}

                        @include('console_j_v_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
