@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Finance Item Category Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($financeItemCategoryMaster, ['route' => ['financeItemCategoryMasters.update', $financeItemCategoryMaster->id], 'method' => 'patch']) !!}

                        @include('finance_item_category_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection