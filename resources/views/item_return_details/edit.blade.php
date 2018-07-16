@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Return Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($itemReturnDetails, ['route' => ['itemReturnDetails.update', $itemReturnDetails->id], 'method' => 'patch']) !!}

                        @include('item_return_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection