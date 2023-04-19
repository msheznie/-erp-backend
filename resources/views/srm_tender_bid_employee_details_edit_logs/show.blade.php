@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Srm Tender Bid Employee Details Edit Log
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('srm_tender_bid_employee_details_edit_logs.show_fields')
                    <a href="{{ route('srmTenderBidEmployeeDetailsEditLogs.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
