@extends('layouts.appuni')

@section('content')
    @section('content_title', $oCourse->course)
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    @if (! isset($oCourse->cover))
                        <img src="{{ asset('img/aceites.jpg') }}" class="img-fluid rounded-start" alt="...">
                    @elseif ($oCourse->cover->file_type == 'video')
                        <video id="idVideo" controls="" autoplay="" name="media" width="100%" height="100%">
                            <source id="idSource" src="{{ $oCourse->cover->view_path }}" type="video/mp4">
                        </video>
                    @else
                        <img src="{{ $oCourse->cover->view_path }}" alt="" class="img-fluid rounded-start" width="100%" height="100%">
                    @endif
                </div>
                <div class="col-md-9">
                    <p>{{ $oCourse->description }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    
                </div>
                <div class="col-md-9" style="text-align: center">
                    <h4>Temario y recursos del curso</h4>
                </div>
            </div>
            <div class="row">                
                <div class="col-md-3 text-white">
                    <div class="row" style="background-color: #1173B0; border-radius: 15%">
                        <div class="col-12">
                            <b>Para este curso vas a necesitar:</b>
                        </div>
                    </div>
                    <div class="row" style="background-color: #1173B0; border-radius: 15%">
                        <div class="col-4" style="text-align: center">
                            <i class='bx bxs-graduation bx-flashing'></i>
                        </div>
                        <div class="col-8">
                            <b>80 Calificación mín.</b>
                        </div>
                    </div>
                    <div class="row" style="background-color: #1173B0; border-radius: 15%">
                        <div class="col-4" style="text-align: center">
                            <i class='bx bxs-coin bx-flashing'></i>
                        </div>
                        <div class="col-8">
                            <b>Puedes ganar {{ $oCourse->university_points }} puntos universitarios</b>
                        </div>
                    </div>
                    @if ($aGrade[0])
                        <br>
                        <a href="{{ route('certificate', [config('csys.elem_type.COURSE'), $oCourse->id_course, $oCourse->id_assignment]) }}" target="_blank">
                            <div class="row" style="background-color: #F3D62D; color: black; border-radius: 15%">
                                <div class="col-4" style="text-align: center">
                                    <br>
                                    <i class='bx bxs-file-doc bx-md bx-flashing'></i>
                                </div>
                                <div class="col-8">
                                    <b>¡Aprobaste el curso!</b>
                                    <p>Descarga tu constancia</p>
                                </div>
                            </div>
                        </a>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="accordion" id="accordionThemes">
                        @foreach ($oCourse->lTopics as $topic)   
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="{{ 'heading'.$topic->id_topic }}">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#{{ 'collapse'.$topic->id_topic }}" aria-expanded="true" aria-controls="collapseOne">
                                    {{ $topic->topic }}
                                    {!! $topic->ended != null ? '<span style="color: green" class="success"><b>&nbsp;(Aprobaste este tema)</b><i class="bx bx-check"></i></span>' : '' !!}
                                    </button>
                                </h2>
                                <div id="{{ 'collapse'.$topic->id_topic }}" class="accordion-collapse collapse show" aria-labelledby="{{ 'heading'.$topic->id_topic }}" data-bs-parent="#accordionThemes">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <ul class="list-group">
                                                @foreach ($topic->lSubtopics as $subtopic)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            {{ $subtopic->subtopic }}
                                                            {!! $subtopic->ended != null ? '<span style="color: green" class="success"><i class="bx bxs-check-circle"></i></span>' : '' !!}
                                                        </div>
                                                        <a href="{{ route('uni.courses.course.play', [$subtopic->id_subtopic, $takeGrouper, $idAssignment]) }}" class="btn {{ $subtopic->ended != null ? "btn-success" : "btn-dark" }}">{{ $subtopic->ended != null ? "Ver contenido" : "Comenzar" }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection