@extends('layouts.appuni')

@include('uni.kardex.sectionjs')

@section('content')
@section('content_title', $student_name != '' ? 'Avance: '.$student_name : 'Mi avance')

<h5><b>Cuadrantes:</b></h5>
<br>
<div class="accordion accordion-flush" id="accordionFlushExample">
    @foreach ($areas as $area)
        <div class="accordion-item accordion-item-cuadrante">
            <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button accordion-button-cuadrante collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#curse{{ $area->id_assignment }}" aria-expanded="false"
                    aria-controls="curse{{ $area->id_assignment }}">
                    <div class="row" style="width: 100%;">
                        <div class="col-md-4">
                            <div class="fw-bold">
                                @if (!$area->is_active)
                                    {{ $area->knowledge_area }}
                                @else
                                    <a href="#" class="btn" style="width: 90%; height: 5rem; background-color: #17D7EC;"
                                        onclick="redirectFunction('{{ route('uni.modules.index', [$area->id_assignment, $area->knowledge_area_id]) }}');">
                                        {{ $area->knowledge_area }}
                                    </a>
                                @endif
                            </div>
                            <br>
                            Vigencia:
                            <i>{{ \Carbon\Carbon::parse($area->dt_assignment)->format('d-M-Y') . ' - ' . \Carbon\Carbon::parse($area->dt_end)->format('d-M-Y') }}</i>
                            <br>
                            <div>
                                <span>Avance: </span>
                                <b>{{ $area->completed_percent }}%</b>
                            </div>
                            <div class="progress" style="width:70%; background-color: #9F9F9F">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width:{{ $area->completed_percent }}%" aria-valuenow="25" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <br>
                        </div>
                        <div class="col-md-8">
                            <div style="text-align: left; margin-left: 5px;">
                                <span>Modulos terminados: {{ $area->end_modules }} de
                                    {{ count($area->modules) }}</span><br>
                                <span>Duración: {{ $area->duracion }}</span><br>
                                <span>Calificación promedio: </span>
                                <span class="badge bg-secondary rounded-pill">{{ $area->promedio }}</span>
                                @if ($area->has_document && $area->elem_status_id == 3 && $area->grade[0])
                                    <br>
                                    <span>Terminado: </span>
                                    <span style="color: black" class="badge bg-info rounded-pill">
                                        {{ \Carbon\Carbon::parse($area->grade[2])->diffForHumans() }}</span>
                                    <br>
                                    <br>
                                    <a type="button" class="btn btn-warning"
                                        href="{{ route('certificate', [config('csys.elem_type.AREA'), $area->id_knowledge_area, $area->id_assignment]) }}"
                                        target="_blank">
                                        <span>
                                            Certificado
                                            <i class='bx bxs-file-doc'></i>
                                        </span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="curse{{ $area->id_assignment }}" class="accordion-collapse collapse"
                aria-labelledby="flush-headingOne">
                <div class="accordion-body">
                    <h5><b style="color: white;">Modulos {{ $area->knowledge_area }}:</b></h5>
                    @foreach ($area->modules as $module)
                        <div class="accordion-item accordion-item-module">
                            <h2 class="accordion-header" id="flush-headingTwo">
                                <button class="accordion-button accordion-button-module collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#module{{ $module->id_module }}" aria-expanded="false"
                                    aria-controls="module{{ $module->id_module }}">
                                    <div class="row" style="width: 100%;">
                                        <div class="col-md-4">
                                            @if (\Carbon\Carbon::parse($module->dt_close)->lt(\Carbon\Carbon::today()) || \Carbon\Carbon::parse($module->dt_open)->gt(\Carbon\Carbon::today()) || ($module->is_closed))
                                                <div class="fw-bold">{{ $module->module }}</div>
                                            @else
                                                <div class="fw-bold">
                                                    <a href="#" class="btn btn-info"
                                                        style="width: 90%; height: 4.5rem; background-color: rgb(0, 255, 170)"
                                                        onclick="redirectFunction('{{ route('uni.courses.index', [$area->id_assignment, $module->id_module]) }}');">
                                                        {{ $module->module }}
                                                    </a>
                                                </div>
                                            @endif
                                            <br>
                                            Vigencia:
                                            {{-- <i>{{ \Carbon\Carbon::parse($area->dt_assignment)->format('d-M-Y') . ' - ' . \Carbon\Carbon::parse($area->dt_end)->format('d-M-Y') }}</i> --}}
                                            <i>{{ \Carbon\Carbon::parse($module->dt_open)->format('d-M-Y') . ' - ' . \Carbon\Carbon::parse($module->dt_close)->format('d-M-Y') }}</i>
                                            <br>
                                            <span>Avance: </span>
                                            <b>{{ $module->completed_percent }}%</b>
                                            <div class="progress" style="width:70%; background-color: #9F9F9F">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                    style="width:{{ $module->completed_percent }}%"
                                                    aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <br>
                                        </div>
                                        <div class="col-md-8">
                                            <div style="text-align: left; margin-left: 10px;">
                                                <span>Cursos terminados: {{ $module->end_courses }} de
                                                    {{ count($module->courses) }}</span><br>
                                                <span>Calificación promedio: </span>
                                                <span
                                                    class="badge bg-secondary rounded-pill">{{ $module->promedio }}</span>
                                                @if ($module->has_document && $module->elem_status_id == 3 && $module->grade[0])
                                                    <br>
                                                    <span>Terminado: </span>
                                                    <span style="color: black" class="badge bg-info rounded-pill">
                                                        {{ \Carbon\Carbon::parse($module->grade[2])->diffForHumans() }}
                                                    </span>
                                                    <br>
                                                    <br>
                                                    <a type="button" class="btn btn-warning"
                                                        href="{{ route('certificate', [config('csys.elem_type.MODULE'), $module->id_module, $module->id_assignment]) }}"
                                                        target="_blank">
                                                        <span>
                                                            Reconocimiento
                                                            <i class='bx bxs-file-doc'></i>
                                                        </span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="module{{ $module->id_module }}" class="accordion-collapse collapse"
                                aria-labelledby="flush-headingTwo">
                                <div class="accordion-body">
                                    <h5><b style="color: white;">Cursos {{ $module->module }}:</b></h5>
                                    @foreach ($module->courses as $course)
                                        <div class="accordion-item accordion-item-course">
                                            <h2 class="accordion-header" id="flush-headingTwo">
                                                <button class="accordion-button accordion-button-course collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#course{{ $course->id_course }}"
                                                    aria-expanded="false"
                                                    aria-controls="module{{ $course->id_course }}">
                                                    <div class="row" style="width: 100%;">
                                                        <div class="col-md-4">
                                                            <div class="fw-bold">
                                                                {{ $course->course }}
                                                            </div>
                                                            <br>
                                                            <span>Avance: </span>
                                                            <b>{{ $course->completed_percent }}%</b>
                                                            <div class="progress"
                                                                style="width:70%; background-color: #9F9F9F">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width:{{ $course->completed_percent }}%"
                                                                    aria-valuenow="25" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <br>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <div style="margin-left: 5px;">
                                                                <span>Calificación promedio:</span>
                                                                <span
                                                                    class="badge bg-secondary rounded-pill">{{ $course->grade[1] == null || $course->grade[1] == 0 ? '-' : $course->grade[1] }}</span>
                                                                @if ($course->has_document && $course->elem_status_id == 3 && $course->grade[0])
                                                                    <br>
                                                                    <span>Terminado: </span>
                                                                    <span style="color: black" class="badge bg-info rounded-pill">
                                                                        {{ \Carbon\Carbon::parse($course->grade[2])->diffForHumans() }}
                                                                    </span>
                                                                    <br>
                                                                    <br>
                                                                    <a type="button" class="btn btn-warning"
                                                                        href="{{ route('certificate', [config('csys.elem_type.COURSE'), $course->id_course, $course->id_assignment]) }}"
                                                                        target="_blank">
                                                                        <span>
                                                                            Constancia
                                                                            <i class='bx bxs-file-doc'></i>
                                                                        </span>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </button>
                                            </h2>
                                            <div id="course{{ $course->id_course }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="flush-headingTwo">
                                                <br>
                                                <div class="accordion-body">
                                                    <label style="color: white;">Contenido del curso:</label>
                                                    @foreach ($course->lTopics as $topic)
                                                        <li class="list-group-item d-flex justify-content-between align-items-start">
                                                            <div class="row" style="width: 100%;">
                                                                <div class="col-md-10">
                                                                    <div class="fw-bold">
                                                                        {{ 'Tema: ' . $topic->topic }}
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <span class="badge bg-primary rounded-pill">{{ $topic->grade[1] == null || $topic->grade[1] == 0 ? '-' : "Calificación: ".$topic->grade[1] }}</span>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                @foreach ($topic->lSubTopics as $subtopic)
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                        <div class="row" style="width: 100%;">
                                                                            <div class="col-md-10">
                                                                                {{ 'Subtema: ' . $subtopic->subtopic }}
                                                                                <br>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <span class="badge bg-primary rounded-pill">{{ $subtopic->grade[1] == null || $subtopic->grade[1] == 0 ? '-' : "Calificación: ".$subtopic->grade[1] }}</span>
                                                                            </div>
                                                                    </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <br>
                    @endforeach
                </div>
            </div>
        </div>
        <br>
        <br>
    @endforeach
</div>
@endsection

@section('scripts_section')
<script>
    function redirectFunction(ruta) {
        location.assign(ruta);
    }
</script>
@endsection
