@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Cust Receive Payment Det Reffered History
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('cust_receive_payment_det_reffered_histories.show_fields')
                    <a href="{!! route('custReceivePaymentDetRefferedHistories.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
