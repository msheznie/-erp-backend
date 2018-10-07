@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accruaval From O P Master
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'accruavalFromOPMasters.store']) !!}

                        @include('accruaval_from_o_p_masters.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
