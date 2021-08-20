<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Univ AETH Notificación</title>
    <link href="{{ asset('bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
</head>

<body>
    <br>
    <br>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card text-center">
                    <div class="card-header">
                        Notificación UNIVAETH
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Premio canjeado</h5>
                        <p class="card-text">
                            Alumno: <b>{{ \Auth::user()->full_name }}</b>
                            <br> Premio: <b>{{ $oGift->code.'-'.$oGift->gift }}</b>
                            <br> Puntos: <b>{{ $oGift->points_value }}</b>
                            <br> Puntos restantes: <b>{{ $points }}</b>
                        </p>
                        <a href="#" class="btn btn-primary">Ir a Universidad</a>
                    </div>
                    <div class="card-footer text-muted">
                        {{ \Carbon\Carbon::now()->toDateTimeString() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>