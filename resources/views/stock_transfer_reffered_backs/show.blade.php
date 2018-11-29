@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Stock Transfer Reffered Back
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('stock_transfer_reffered_backs.show_fields')
                    <a href="{!! route('stockTransferRefferedBacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
