@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Disposal Referred
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetDisposalReferred, ['route' => ['assetDisposalReferreds.update', $assetDisposalReferred->id], 'method' => 'patch']) !!}

                        @include('asset_disposal_referreds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection