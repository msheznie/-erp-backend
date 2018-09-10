@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Warehouse Items
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($warehouseItems, ['route' => ['warehouseItems.update', $warehouseItems->id], 'method' => 'patch']) !!}

                        @include('warehouse_items.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection