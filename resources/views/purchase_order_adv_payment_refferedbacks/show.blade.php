@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Adv Payment Refferedback
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('purchase_order_adv_payment_refferedbacks.show_fields')
                    <a href="{!! route('purchaseOrderAdvPaymentRefferedbacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
