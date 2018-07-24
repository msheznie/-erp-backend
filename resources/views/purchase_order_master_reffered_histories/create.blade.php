@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Master Reffered History
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'purchaseOrderMasterRefferedHistories.store']) !!}

                        @include('purchase_order_master_reffered_histories.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
