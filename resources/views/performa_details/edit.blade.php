@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Performa Details
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($performaDetails, ['route' => ['performaDetails.update', $performaDetails->id], 'method' => 'patch']) !!}

                        @include('performa_details.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection