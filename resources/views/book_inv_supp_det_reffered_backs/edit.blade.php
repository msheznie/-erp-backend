@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Book Inv Supp Det Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bookInvSuppDetRefferedBack, ['route' => ['bookInvSuppDetRefferedBacks.update', $bookInvSuppDetRefferedBack->id], 'method' => 'patch']) !!}

                        @include('book_inv_supp_det_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection