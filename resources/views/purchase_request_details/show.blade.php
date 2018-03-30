@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Request Details
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('purchase_request_details.show_fields')
                    <a href="{!! route('purchaseRequestDetails.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
