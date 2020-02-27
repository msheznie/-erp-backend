@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Chart Of Account Allocation Detail History
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('chart_of_account_allocation_detail_histories.show_fields')
                    <a href="{!! route('chartOfAccountAllocationDetailHistories.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
