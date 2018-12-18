@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Customer Invoice Collection Detail
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('customer_invoice_collection_details.show_fields')
                    <a href="{!! route('customerInvoiceCollectionDetails.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
