@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Purchase Order Category
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($purchaseOrderCategory, ['route' => ['purchaseOrderCategories.update', $purchaseOrderCategory->id], 'method' => 'patch']) !!}

                        @include('purchase_order_categories.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection