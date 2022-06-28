<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UVAETH Notificación</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-header">
                        Notificación UVAETH
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Te asignamos un cuadrante nuevo</h5>
                        <p class="card-text">
                            Cuadrante: <b>{{ $oAssignment->knowledge_area }}</b>
                            <br>
                            Número: <b>{{ $oStudent->num_employee }}</b>
                            Alumno: <b>{{ $oStudent->full_name }}</b>
                            Te recordamos tu usuario: <b>{{ $oStudent->username }}</b>
                            <br> Fecha inicio: <b>{{ \Carbon\Carbon::parse($oAssignment->dt_assignment)->format('d-m-Y') }}</b>
                            <br> Fecha límite: <b>{{ \Carbon\Carbon::parse($oAssignment->dt_end)->format('d-m-Y') }}</b>
                        </p>
                        <a href="{{ ENV('APP_URL_MAIL') }}" class="btn btn-primary">Ir a Universidad</a>
                    </div>
                    <div class="card-footer text-muted">
                        {{ \Carbon\Carbon::now()->format('d-m-Y H:i:s') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>