@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Asset Capitalizatio Det Referred
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($assetCapitalizatioDetReferred, ['route' => ['assetCapitalizatioDetReferreds.update', $assetCapitalizatioDetReferred->id], 'method' => 'patch']) !!}

                        @include('asset_capitalizatio_det_referreds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection