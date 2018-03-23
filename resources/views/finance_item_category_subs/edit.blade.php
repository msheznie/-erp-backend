@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Finance Item Category Sub
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($financeItemCategorySub, ['route' => ['financeItemCategorySubs.update', $financeItemCategorySub->id], 'method' => 'patch']) !!}

                        @include('finance_item_category_subs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection