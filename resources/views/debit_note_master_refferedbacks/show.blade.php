@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Debit Note Master Refferedback
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('debit_note_master_refferedbacks.show_fields')
                    <a href="{!! route('debitNoteMasterRefferedbacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
