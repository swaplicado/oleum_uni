@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oProfileTable = $('#profile').DataTable({
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }
            },
            "colReorder": true,
            "responsive": true,
            "columnDefs": [
                    { responsivePriority: 1, targets: [2,3] }
                ],
            "dom": 'Bfrtip',
            "lengthMenu": [
                [ 10, 25, 50, 100, -1 ],
                [ 'Mostrar 10', 'Mostrar 25', 'Mostrar 50', 'Mostrar 100', 'Mostrar todo' ]
            ],
            "buttons": [
                    'pageLength',
                    {
                        extend: 'copy',
                        text: 'Copiar'
                    }, 
                    'csv', 
                    'excel', 
                    {
                        extend: 'print',
                        text: 'Imprimir'
                    }
                ]
        });
    } );
</script>
@endsection

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
                        <div class="col-12 col-md-6">
                            <label class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->username }}" placeholder="Nombre de usuario" readonly>
                        </div>
                        <div class="col-12 col-md-6">
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
                        <div class="col-12 col-md-6">
                            <label class="form-label">Puesto</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->job->job }}" placeholder="Puesto" readonly>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Departamento</label>
                            <input type="text" class="form-control" placeholder="Departamento" value="{{ \Auth::user()->job->department->department }}" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Jefe directo</label>
                            <input type="text" value="{{ $boss == null ? '-' : $boss->full_name }}" class="form-control" placeholder="Jefe Directo" readonly>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Sucursal</label>
                            <input type="text" class="form-control" value="{{ \Auth::user()->branch->branch }}" placeholder="Sucursal" readonly>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" value="{{ \Auth::user()->email }}" placeholder="mail@dominio.com" readonly>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Contraseña</label><br>
                            <a href="{{ route('change.pass') }}" class="btn btn-warning">Cambiar contraseña <i class='bx bxs-key'></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-md-9">
                            <h5>Puntos por cambiar y calificaciones</h5>
                        </div>
                        <div class="col-md-3">
                            <h5>Disponibles: <span style="font-size: 110%" class="badge bg-primary">{{ $oPoints != null ? $oPoints->points : 0 }}</span></h5>
                        </div>
                    </div>
                    <br>
                    <table id="profile" class="display stripe hover row-border order-column" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Ganados</th>
                                <th>Descontados</th>
                                <th>T. mov</th>
                                <th>Curso</th>
                                <th>Premio</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach ($lPoints as $pointRow)
                                <tr>
                                    <td>
                                        {{ $pointRow->index }}
                                    </td>
                                    <td>
                                        {{ $pointRow->dt_date }}
                                    </td>
                                    <td>
                                        {{ $pointRow->increment }}
                                    </td>
                                    <td>
                                        {{ $pointRow->decrement }}
                                    </td>
                                    <td>
                                        {{ $pointRow->movement_type }}
                                    </td>
                                    <td>
                                        {{ $pointRow->course }}
                                    </td>
                                    <td>
                                        {{ $pointRow->gift }}
                                    </td>
                                </tr>
                           @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Puntos ganados</th>
                                <th>Puntos perdidos</th>
                                <th>T. mov</th>
                                <th>Curso</th>
                                <th>Premio</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection