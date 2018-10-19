@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Disposal Type
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetDisposalType, ['route' => ['assetDisposalTypes.update', $assetDisposalType->id], 'method' => 'patch']) !!}

                        @include('asset_disposal_types.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection