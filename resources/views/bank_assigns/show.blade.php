@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Bank Assign
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('bank_assigns.show_fields')
                    <a href="{!! route('bankAssigns.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
