@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Issue Master Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($itemIssueMasterRefferedBack, ['route' => ['itemIssueMasterRefferedBacks.update', $itemIssueMasterRefferedBack->id], 'method' => 'patch']) !!}

                        @include('item_issue_master_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection