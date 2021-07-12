@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Cambiar contraseña')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('update.pass') }}" method="POST">
                @method('POST')
                @csrf
                <div class="row align-items-center">
                    <div class="col-3">
                    <label for="inputPassword6" class="col-form-label">Contraseña actual:</label>
                    </div>
                    <div class="col-5">
                    <input type="password" id="inputPassword6" name="current_password" class="form-control" aria-describedby="passwordHelpInline" required>
                    </div>
                </div>
                <br>
                <div class="row align-items-center">
                    <div class="col-3">
                    <label for="inputPassword5" class="col-form-label">Contraseña nueva:</label>
                    </div>
                    <div class="col-5">
                    <input type="password" id="inputPassword5" name="new_password" class="form-control" aria-describedby="passwordHelpInline" required>
                    </div>
                    <div class="col-auto">
                    <span id="passwordHelpInline" class="form-text">
                        Debe ser de al menos 2 caracteres.
                    </span>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-3">
                    <label for="inputPassword4" class="col-form-label">Confirma nueva contraseña:</label>
                    </div>
                    <div class="col-5">
                    <input type="password" id="inputPassword4" name="confirmed_new_password" class="form-control" aria-describedby="passwordHelpInline" required>
                    </div>
                </div>
                <br>
                <br>
                <div class="row">
                    <div class="col-10"></div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-primary" href="">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection