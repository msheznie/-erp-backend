@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Finance Itemcategory Sub
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($financeItemcategorySub, ['route' => ['financeItemcategorySubs.update', $financeItemcategorySub->id], 'method' => 'patch']) !!}

                        @include('finance_itemcategory_subs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection