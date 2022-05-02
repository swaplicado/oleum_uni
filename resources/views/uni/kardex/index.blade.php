@extends('layouts.appuni')

@include('uni.kardex.sectionjs')

@section('content')
    @section('content_title', 'Mi avance')

    <div class="row">
        <div class="col-12">
            <h5>Competencias:</h5>
            <br>
            <div class="accordion accordion-flush" id="accordionFlushExample">
                @foreach ($areas as $area)
                    <div class="accordion-item" style="border: solid 0.03cm #7E7E7E">
                        <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#curse{{$area->id_assignment}}" aria-expanded="false" aria-controls="curse{{$area->id_assignment}}">
                            <div class="" style="width: 27%; border-right: solid 0.03cm black">
                                <div class="fw-bold">
                                    @if (!$area->is_active)
                                        {{ $area->knowledge_area }}
                                    @else
                                        <a href="#" class="btn btn-primary" onclick="redirectFunction('{{ route('uni.modules.index', [$area->id_assignment, $area->knowledge_area_id]) }}');">
                                            {{ $area->knowledge_area }}
                                        </a>
                                    @endif
                                </div>
                                Cursada: <i>{{ $area->dt_assignment.' - '.$area->dt_end }}</i>
                                <br>                    
                                <div>
                                    <span>Avance: </span>
                                    <b>{{$area->completed_percent}}%</b>
                                </div>
                                <div class="progress" style="width:70%; background-color: #9F9F9F">
                                    <div class="progress-bar bg-success" role="progressbar" style="width:{{$area->completed_percent}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div style="text-align: left; margin-left: 5px;">
                                <span>Modulos terminados: {{$area->end_modules}} de {{count($area->modules)}}</span><br>
                                <span>Duración: {{$area->duracion}}</span><br>
                                <span>Calificación promedio: </span>
                                <span class="badge bg-primary rounded-pill">{{ $area->promedio}}</span>
                                <br>
                                @if ($area->has_document && $area->elem_status_id == 3 && $area->grade[0])
                                    <a href="{{ route('certificate', [config('csys.elem_type.AREA'), $area->id_knowledge_area, $area->id_assignment]) }}" target="_blank">
                                        <i class='bx bxs-file-doc'></i>
                                    </a>
                                @endif
                            </div>
                        </button>
                        </h2>
                        <div id="curse{{$area->id_assignment}}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne">
                        <div class="accordion-body">
                            <h5>Modulos {{ $area->knowledge_area }}:</h5>
                            @foreach ($area->modules as $module)
                                <div class="accordion-item" style="border: solid 0.03cm #AEAEAE">
                                    <h2 class="accordion-header" id="flush-headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#module{{$module->id_module}}" aria-expanded="false" aria-controls="module{{$module->id_module}}">
                                            <div class="" style="width: 27%; border-right: solid 0.03cm black">
                                                @if (!$area->is_active)
                                                    <div class="fw-bold" style="color: brown">{{ $module->module }}</div>
                                                @else
                                                    <div class="fw-bold">
                                                        <a href="#" class="btn btn-primary" onclick="redirectFunction('{{ route('uni.courses.index', [$area->id_assignment, $module->id_module]) }}');">
                                                            {{ $module->module }}
                                                        </a>
                                                    </div>
                                                @endif
                                                Cursado: <i>{{ $area->dt_assignment.' - '.$area->dt_end }}</i>
                                                <br>
                                                <span>Avance: </span>
                                                <b>{{$module->completed_percent}}%</b>
                                                <div class="progress" style="width:70%; background-color: #9F9F9F">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width:{{$module->completed_percent}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div style="text-align: left; margin-left: 5px;">
                                                <span>Cursos terminados: {{$module->end_courses}} de {{count($module->courses)}}</span><br>
                                                <span>Calificación promedio: </span>
                                                <span class="badge bg-secondary rounded-pill">{{ $module->promedio }}</span>
                                                <br>
                                                @if ($module->has_document && $module->elem_status_id == 3 && $module->grade[0])
                                                    <a href="{{ route('certificate', [config('csys.elem_type.MODULE'), $module->id_module, $module->id_assignment]) }}" target="_blank">
                                                        <i class='bx bxs-file-doc'></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="module{{$module->id_module}}" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo">
                                        <div class="accordion-body">
                                            <h5>Cursos {{ $module->module }}:</h5>
                                            @foreach ($module->courses as $course)
                                                <div class="accordion-item" style="border: solid 0.03cm #CECECE">
                                                    <h2 class="accordion-header" id="flush-headingTwo">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#course{{$course->id_course}}" aria-expanded="false" aria-controls="module{{$course->id_course}}">
                                                            <div class="" style = "width: 27%; border-right: solid 0.03cm black">
                                                                <div class="fw-bold" style="color: #f7990c">{{ $course->course }}</div>
                                                                <br>
                                                                <span>Avance: </span>
                                                                <b>{{$course->completed_percent}}%</b>
                                                                <div class="progress" style="width:70%; background-color: #9F9F9F">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width:{{$course->completed_percent}}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                            <div style = "margin-left: 5px;">
                                                                <span>Calificación promedio:</span>
                                                                <span class="badge bg-primary rounded-pill">{{ $course->grade[1] == null || $course->grade[1] == 0 ? "-" : $course->grade[1] }}</span>
                                                                <br>
                                                                @if ($course->has_document && $course->elem_status_id == 3 && $course->grade[0])
                                                                    <a href="{{ route('certificate', [config('csys.elem_type.COURSE'), $course->id_course, $course->id_assignment]) }}" target="_blank">
                                                                        <i class='bx bxs-file-doc'></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </button>
                                                    </h2>
                                                    <div id="course{{$course->id_course}}" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo">
                                                        <div class="accordion-body">
                                                            @foreach ($course->lTopics as $topic)
                                                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                                                    <div class="ms-2 me-auto">
                                                                        <div class="fw-bold">{{ "Tema: ".$topic->topic }}</div>
                                                                    </div>
                                                                    <span class="badge bg-primary rounded-pill">{{ $topic->grade[1] == null || $topic->grade[1] == 0 ? "-" : $topic->grade[1] }}</span>
                                                                </li>
                                                                <div class="card-body">
                                                                    <ul class="list-group">
                                                                        @foreach ($topic->lSubTopics as $subtopic)    
                                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                {{ "Subtema: ".$subtopic->subtopic }}
                                                                            <span class="badge bg-primary rounded-pill">{{ $subtopic->grade[1] == null || $subtopic->grade[1] == 0 ? "-" : $subtopic->grade[1] }}</span>
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
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('scripts_section')
    <script>
        function redirectFunction(ruta){
            location.assign(ruta);
        }
    </script>
@endsection