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
                            <a href="{{ route('kardex.courses', $module->id_module) }}">Ver Cursos</a>
                        </div>
                        <div style="text-align: center">
                            <span class="badge bg-secondary rounded-pill">100</span>
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