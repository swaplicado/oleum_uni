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
                            <a href="{{ route('kardex.modules', [$area->id_knowledge_area, $area->id_assignment, $student]) }}">Ver Módulos</a>
                        </div>
                        <div style="text-align: center">
                            <span class="badge bg-primary rounded-pill">{{ $area->grade[1] == null || $area->grade[1] == 0 ? "-" : $area->grade[1] }}</span>
                            <br>
                            @if ($area->has_document && $area->elem_status_id == 3 && $area->grade[0])
                                <a href="{{ route('certificate', [config('csys.elem_type.AREA'), $area->id_knowledge_area, $area->id_assignment]) }}" target="_blank">
                                    <i class='bx bxs-file-doc'></i>
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection