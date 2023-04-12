@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Tender Boq Items Edit Log
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('tender_boq_items_edit_logs.show_fields')
                    <a href="{{ route('tenderBoqItemsEditLogs.index') }}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
