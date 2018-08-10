@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Taxdetail
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($taxdetail, ['route' => ['taxdetails.update', $taxdetail->id], 'method' => 'patch']) !!}

                        @include('taxdetails.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection