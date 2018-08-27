@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Direct Receipt Detail
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('direct_receipt_details.show_fields')
                    <a href="{!! route('directReceiptDetails.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
