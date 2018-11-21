@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Payment Referback
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('direct_payment_referbacks.show_fields')
                    <a href="{!! route('directPaymentReferbacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
