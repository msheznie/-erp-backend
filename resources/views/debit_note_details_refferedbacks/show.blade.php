@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Debit Note Details Refferedback
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('debit_note_details_refferedbacks.show_fields')
                    <a href="{!! route('debitNoteDetailsRefferedbacks.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
