@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Gpos Payment Gl Config Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('gpos_payment_gl_config_masters.show_fields')
                    <a href="{!! route('gposPaymentGlConfigMasters.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
