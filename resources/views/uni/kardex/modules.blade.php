@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Mi avance')

    <div class="row">
        <div class="col-12">
            <h5>Módulos:</h5>
            <br>
            <ol class="list-group list-group-numbered">
                @foreach ($lModules as $module)
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold" style="color: brown">{{ $module->module }}</div>
                            Cursado: <i>{{ $module->dt_assignment.' - '.$module->dt_end }}</i>
                            <br>
                            <a href="{{ route('kardex.courses', [$module->id_module, $student]) }}">Ver Cursos</a>
                        </div>
                        <div style="text-align: center">
                            <span class="badge bg-secondary rounded-pill">{{ $module->grade[1] == null || $module->grade[1] == 0 ? "-" : $module->grade[1] }}</span>
                            <br>
                            @if ($module->elem_status_id == 3 && $module->grade[0])
                                <a href="{{ route('certificate', [config('csys.elem_type.MODULE'), $module->id_module, $module->id_assignment]) }}" target="_blank">
                                    <i class='bx bx-paperclip'></i>
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endsection