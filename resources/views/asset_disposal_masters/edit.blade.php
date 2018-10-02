@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Disposal Master
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetDisposalMaster, ['route' => ['assetDisposalMasters.update', $assetDisposalMaster->id], 'method' => 'patch']) !!}

                        @include('asset_disposal_masters.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection