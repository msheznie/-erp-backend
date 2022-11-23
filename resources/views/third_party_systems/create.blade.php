@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Third Party Systems
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'thirdPartySystems.store']) !!}

                        @include('third_party_systems.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
