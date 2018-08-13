@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Client Reference Number Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'itemClientReferenceNumberMasters.store']) !!}

                        @include('item_client_reference_number_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
