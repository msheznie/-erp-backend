@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Po Addons
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row" style="padding-left: 20px">
                    @include('po_addons.show_fields')
                    <a href="{!! route('poAddons.index') !!}" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
@endsection
