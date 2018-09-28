@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Invoice Details Reffered Back
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('direct_invoice_details_reffered_backs.show_fields')
                    <a href="{!! route('directInvoiceDetailsRefferedBacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
