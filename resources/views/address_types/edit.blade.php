@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Address Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($addressType, ['route' => ['addressTypes.update', $addressType->id], 'method' => 'patch']) !!}

                        @include('address_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection