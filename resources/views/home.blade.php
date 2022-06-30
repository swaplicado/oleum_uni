@extends('layouts.appuni')

@section('content')
@section('content_title', $title)
@section('css_section')
    <link rel="stylesheet" type="text/css" href="{{ asset('myapp/css/percent_circle.css') }}" />
@endsection
<div class="row">
    <div class="row">
        <div class="col-md-3">
            <div class="card text-dark bg-light" style="width: 18rem;">
                <div class="card-header bg-light" style="text-align: center">
                    <img src="{{ asset(\Auth::user()->profile_picture) }}" class="card-img-top" alt="..."
                        style="height: 10rem; width: 10rem;">
                </div>
                <div class="card-body">
                    <div style="text-align: center;">
                        <a href="{{ route('profile') }}">
                            <h5 class="card-title">{{ \Auth::user()->username }}</h5>
                        </a>
                    </div>
                    <p><b>Num. empleado: </b>{{ \Auth::user()->num_employee }}</p>
                    <p><b>Puesto: </b>{{ \Auth::user()->job->job }}</p>
                    <a class="btn btn-primary" href="{{ route('kardex.index') }}" target="_blank">Mi avance</a>
                </div>
              </div>
              <br>
        </div>
        <div class="col-md-9">
            @include('carousel')
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <a href="{{ route('areas.index') }}">
                <h2 style="color: #05887F">Cuadrantes...</h2>
            </a>
        </div>
    </div>
    <div class="row">
        @if (count($lAssignments) == 0)
            <h5>No tienes competencias asignadas</h5>
        @endif
        @foreach ($lAssignments as $ka)
            <div class="col-lg-3 col-md-6 col-12 ">
                <a href="{{ route('uni.modules.index', [$ka->id_assignment, $ka->id_knowledge_area]) }}">
                    <div class="card h-100 border-primary text-dark bg-light mb-3" style="max-width: 18rem;">
                        <div class="card-header"
                            style="height: 8rem; background-image: url('{{ asset($ka->content_path) }}'); background-size: 18rem 8rem;">
                            <progress class="progress" value="{{ $ka->completed_percent }}" max="100"
                                style="margin-left: 30%; margin-top: 3%;"></progress>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <h5 class="card-title text-primary">{{ $ka->knowledge_area }}</h5>
                            </li>
                        </ul>
                        <div class="card-body">
                            <p class="card-text">{{ $ka->description }}</p>
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                <button style="width: 95%"
                                    href="{{ route('uni.modules.index', [$ka->id_assignment, $ka->id_knowledge_area]) }}"
                                    class="btn btn-info" type="button">Iniciar cursos</button>
                            </small>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
    <hr>
    <div class="row">
        <div class="col-12">
            <h2 style="color: #05887F">Cursando actualmente...</h2>
        </div>
    </div>
    <div class="row">
        @if (count($lCourses) == 0)
            <h5>No hay nada por aquí...</h5>
        @endif
        @foreach ($lCourses as $course)
            <div class="col-lg-3 col-md-6 col-12">
                <a href="{{ route('uni.courses.course', [$course->id_course, $course->assignment_id]) }}">
                    <div class="card h-100 border-success" style="max-width: 18rem;">
                        <div class="card-header"
                            style="height: 8rem; background-image: url('{{ asset($course->content_path) }}'); background-size: 18rem 8rem;">
                            <progress class="progress" value="{{ $course->completed_percent }}" max="100"
                                style="margin-left: 25%;"></progress>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->course }}</h5>
                            <p class="card-text">{{ $course->description }}</p>
                            <p class="card-text">
                                <small class="text-muted">
                                    {{ 'Comenzado ' . \Carbon\Carbon::parse($course->dtt_take)->diffForHumans() }}
                                </small>
                                <button style="width: 95%"
                                    href="{{ route('uni.courses.course', [$course->id_course, $course->assignment_id]) }}"
                                    class="btn btn-info" type="button">Continuar curso</button>
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
</div>
@endsection
