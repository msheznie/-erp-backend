@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Invoice Direct Det Refferedback
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'customerInvoiceDirectDetRefferedbacks.store']) !!}

                        @include('customer_invoice_direct_det_refferedbacks.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
