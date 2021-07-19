@extends('layouts.appuni')

@section('content')
    @section('content_title', $oCourse->course)
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-md-3">
                    <img src="{{ asset('img/aceites.jpg') }}" class="img-fluid rounded-start" alt="...">
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
                    <div class="row" style="background-color: #59359a; border-radius: 15%">
                        <div class="col-12">
                            <b>Para este curso vas a necesitar:</b>
                        </div>
                    </div>
                    <div class="row" style="background-color: #59359a; border-radius: 15%">
                        <div class="col-4" style="text-align: center">
                            <i class='bx bxs-time-five bx-lg bx-flashing' ></i>
                        </div>
                        <div class="col-8">
                            <b>8 horas de contenido</b>
                        </div>
                    </div>
                    <div class="row" style="background-color: #59359a; border-radius: 15%">
                        <div class="col-4" style="text-align: center">
                            <i class='bx bxs-timer bx-lg bx-flashing'></i>
                        </div>
                        <div class="col-8">
                            <b>1 hora de evaluación</b>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="accordion" id="accordionThemes">
                        @foreach ($oCourse->lTopics as $topic)   
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="{{ 'heading'.$topic->id_topic }}">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#{{ 'collapse'.$topic->id_topic }}" aria-expanded="true" aria-controls="collapseOne">
                                    {{ $topic->topic }}
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
                                                            {!! $subtopic->ended != null ? '<span style="color: green" class="success"><i class="bx bx-check"></i></span>' : '' !!}
                                                        </div>
                                                        @if ($subtopic->ended == null)
                                                            <a href="{{ route('uni.courses.course.play', [$subtopic->id_subtopic, $takeGrouper]) }}" class="btn btn-dark">Comenzar</a>
                                                        @endif
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