@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Item Issue Details Reffered Back
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($itemIssueDetailsRefferedBack, ['route' => ['itemIssueDetailsRefferedBacks.update', $itemIssueDetailsRefferedBack->id], 'method' => 'patch']) !!}

                        @include('item_issue_details_reffered_backs.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection