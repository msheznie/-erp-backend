@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Payment Term Types
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('po_payment_term_types.show_fields')
                    <a href="{!! route('poPaymentTermTypes.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
