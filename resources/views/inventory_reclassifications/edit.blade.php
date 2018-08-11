@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Inventory Reclassification
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($inventoryReclassification, ['route' => ['inventoryReclassifications.update', $inventoryReclassification->id], 'method' => 'patch']) !!}

                        @include('inventory_reclassifications.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection