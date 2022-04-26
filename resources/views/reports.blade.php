@extends('layouts.appuni')

@section('content')
@section('content_title', 'Reportes')
    <div class="row" id="indexModulesApp">
          <div class="col-lg-3 col-md-6 col-12">
            <a href="{{ route('kardex.indexReport') }}">
              <div class="card border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
                <div class="card-body">
                  <b class="card-title">Reporte de resultados</b>
                </div>
              </div>
            </a>
          </div>
    </div>
@endsection