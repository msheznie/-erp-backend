@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Book Inv Supp Master Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($bookInvSuppMasterRefferedBack, ['route' => ['bookInvSuppMasterRefferedBacks.update', $bookInvSuppMasterRefferedBack->id], 'method' => 'patch']) !!}

                        @include('book_inv_supp_master_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection