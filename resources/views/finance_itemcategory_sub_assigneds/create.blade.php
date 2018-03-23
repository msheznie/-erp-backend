@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Finance Itemcategory Sub Assigned
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">

            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'financeItemcategorySubAssigneds.store']) !!}

                        @include('finance_itemcategory_sub_assigneds.fields')

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
