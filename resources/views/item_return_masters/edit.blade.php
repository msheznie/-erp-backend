@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Return Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($itemReturnMaster, ['route' => ['itemReturnMasters.update', $itemReturnMaster->id], 'method' => 'patch']) !!}

                        @include('item_return_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection