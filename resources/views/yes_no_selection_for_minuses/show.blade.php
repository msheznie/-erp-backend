@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Yes No Selection For Minus
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('yes_no_selection_for_minuses.show_fields')
                    <a href="{!! route('yesNoSelectionForMinuses.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
