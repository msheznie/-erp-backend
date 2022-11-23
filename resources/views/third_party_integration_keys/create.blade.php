@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Third Party Integration Keys
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'thirdPartyIntegrationKeys.store']) !!}

                        @include('third_party_integration_keys.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
