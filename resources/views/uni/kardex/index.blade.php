@extends('layouts.appuni')

@include('uni.kardex.sectionjs')

@section('content')
    @section('content_title', 'Mi avance')

    <div class="row">
        <div class="col-12">
            <h5>Competencias:</h5>
            <br>
            <ol class="list-group list-group-numbered">
                @foreach ($areas as $area)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">{{ $area->knowledge_area }}</div>
                            Cursada: <i>{{ $area->dt_assignment.' - '.$area->dt_end }}</i>
                            <br>
                            <a href="{{ route('kardex.modules', $area->id_knowledge_area) }}">Ver Módulos</a>
                        </div>
                        <div style="text-align: center">
                            <span class="badge bg-primary rounded-pill">95</span>
                            <br>
                            <a href="#" target="_blank" >
                                <i class='bx bx-paperclip'></i>
                            </a>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection