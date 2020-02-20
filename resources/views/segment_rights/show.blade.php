@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Segment Rights
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('segment_rights.show_fields')
                    <a href="{!! route('segmentRights.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
