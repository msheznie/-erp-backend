@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Inventory Reclassification Detail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($inventoryReclassificationDetail, ['route' => ['inventoryReclassificationDetails.update', $inventoryReclassificationDetail->id], 'method' => 'patch']) !!}

                        @include('inventory_reclassification_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection