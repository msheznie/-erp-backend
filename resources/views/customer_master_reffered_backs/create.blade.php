@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Master Reffered Back
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'customerMasterRefferedBacks.store']) !!}

                        @include('customer_master_reffered_backs.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
