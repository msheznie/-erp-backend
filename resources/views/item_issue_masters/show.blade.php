@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Issue Master
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('item_issue_masters.show_fields')
                    <a href="{!! route('itemIssueMasters.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
