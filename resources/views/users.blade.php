@extends('layouts.appuni')

@section('scripts_section')
    <script type="text/javascript">
        $(document).ready(function() {
            var oProfileTable = $('#users').DataTable({
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
                    { responsivePriority: 1, targets: 4 }
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
    <script type="text/javascript">
        function GlobalData () {
            this.mailroute = <?php echo json_encode( route($mailroute) ) ?>;
            this.userroute = <?php echo json_encode( route($userroute) ) ?>;
            this.passroute = <?php echo json_encode( route($passroute) ) ?>;
        }
        
        var oServerData = new GlobalData();
    </script>
@endsection

@section('content')
    @section('content_title', 'Usuarios')
    <div class="row" id="usersApp">
        <div class="col-12">
            <table id="users" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Num</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th style="text-align: center">-</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($lUsers as $user)
                   <tr>
                        <td>{{ str_pad($user->num_employee, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td style="text-align: center">
                            <a href="#" v-on:click="editPassword({{ $user->id }})" title="Cambiar contraseña">
                                <i class='bx bxs-key'></i>
                            </a>
                            <a href="#" v-on:click="editMail({{ $user->id }},'{{ $user->email }}')" title="Actualizar correo">
                                <i class='bx bx-envelope'></i>
                            </a>
                            <a href="#" v-on:click="editUsername({{ $user->id }},'{{ $user->username }}')" title="Modificar nombre de usuario">
                                <i class='bx bxs-user-circle'></i>
                            </a>
                        </td>
                    </tr>
                   @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Num</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th style="text-align: center">-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('adm.upd_mail_modal')
        @include('adm.upd_pass_modal')
        @include('adm.upd_username_modal')
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/adm/VueUsers.js') }}"></script>
@endsection