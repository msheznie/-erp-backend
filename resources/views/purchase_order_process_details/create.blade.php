@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Process Details
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'purchaseOrderProcessDetails.store']) !!}

                        @include('purchase_order_process_details.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
