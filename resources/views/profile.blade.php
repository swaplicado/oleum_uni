@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Mi perfil')
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12 col-lg-4 col-md-4">
                    <div class="card" style="width: 18rem;">
                        <img src="{{ asset(\Auth::user()->profile_picture) }}" width="100%" height="100%" class="rounded mx-auto d-block" alt="">
                        <div class="card-body">
                            <a href="{{ route('change.avatar') }}" style="width: 100%" class="btn btn-info">Cambiar avatar</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-8 col-md-8">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->username }}" placeholder="Nombre de usuario" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Número de empleado</label>
                            <input style="text-align: center" type="text" class="form-control" value="{{ \Auth::user()->num_employee }}" placeholder="Número de empleado" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <label class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->full_name }}" placeholder="Nombre completo" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Puesto</label>
                            <input type="text" class="form-control" placeholder="Puesto" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" placeholder="Departamento" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Jefe Directo</label>
                            <input type="text" class="form-control" placeholder="Jefe Directo" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Sucursal</label>
                            <input type="text" class="form-control" placeholder="Sucursal" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" value="{{ \Auth::user()->email }}" placeholder="mail@dominio.com" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Contraseña</label><br>
                            <a href="{{ route('change.pass') }}" class="btn btn-warning">Cambiar contraseña <i class='bx bxs-key'></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <label for="">Puntos por cambiar y calificaciones</label>
                </div>
            </div>
        </div>
    </div>
@endsection