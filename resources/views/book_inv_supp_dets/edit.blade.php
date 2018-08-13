@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Book Inv Supp Det
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bookInvSuppDet, ['route' => ['bookInvSuppDets.update', $bookInvSuppDet->id], 'method' => 'patch']) !!}

                        @include('book_inv_supp_dets.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection