@extends('layouts.appuni')

@section('scripts_section')
<script type="text/javascript">
    $(document).ready(function() {
        var oKareasTable = $('#modules_table').DataTable({
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
                    { responsivePriority: 1, targets: [5, 6] }
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
<script>
    $(function () {
        var statusModal = document.getElementById('statusModal');
        statusModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var recipient = button.getAttribute('data-bs-whatever');
            var id = button.getAttribute('data-id');
            var modalTitle = statusModal.querySelector('.modal-title');
            var modalBodyInput = document.getElementById('row_id');

            modalTitle.textContent = recipient;
            modalBodyInput.value = id;
        });
        statusModal.addEventListener('hidden.bs.modal', function (event) {
            $(this).find('#mform')[0].reset();
        });
    })
</script>
<script>
    function moduleDelete(module_id, name, ruta) {
            Swal.fire({
                title: 'Desea eliminar?',
                text: name,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = ruta;
                    url = url.replace(':id',module_id);
                    var fm = document.getElementById('form_delete');
                    fm.setAttribute('action', url);
                    fm.submit();
                }
            });
        }
</script>
@endsection

@section('content')
<form id="form_delete" class="d-inline" method="POST" style="display: none;">
    @csrf @method("delete")
</form>
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="mform" action="{{route('modules.status')}}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="estatus" class="form-label">Estatus:</label>
                        <select class="form-select" name="estatus" required>
                            <option value="" selected>Selecciona estatus</option>
                            <option value="1">Nuevo</option>
                            <option value="2">Editando</option>
                            <option value="3">Publicado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="hidden" name="row_id" id="row_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    @section('content_title', $title)
    <a id="rightnew" href="{{ route($newRoute, $kArea) }}" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </a>
    <div class="row" id="divPrerrequisites">
        <div class="col-md-12">
            <table id="modules_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>Secuencia</th>
                        <th>Estatus</th>
                        <th>Cuadrante</th>
                        <th>Cursos</th>
                        <th>Pre</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lModules as $module)
                        <tr>
                            <td>{{ $module->module }}</td>
                            <td>{{ $module->description }}</td>
                            <td>{{ $module->seq_code }}</td>
                            <td>
                                {{ $module->status_code }}
                                <a href="#" data-bs-toggle="modal" data-bs-target="#statusModal" 
                                    data-bs-whatever="{{ $module->module }}" data-id="{{$module->id_module}}">
                                    <span class="bx bx-edit-alt"></span>
                                </a>
                            </td>
                            <td>{{ $module->knowledge_area }}</td>
                            <td style="text-align: center">
                                <a title="Ver cursos" href="{{ route('courses.index', $module->id_module) }}">
                                    <i class='bx bxs-category'></i>
                                </a>
                                <a title="Editar Módulo" href="{{ route('modules.edit', $module->id_module) }}">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <a href="#" v-on:click="showCopyElementModal({{$module->id_module}}, 'module');">
                                    <i class='bx bx-export'></i>
                                </a>
                            </td>
                            <td style="text-align: center">
                                <a href="#" v-on:click="showPreviousModal({{ config('csys.elem_type.MODULE') }}, {{ $module->id_module }}, '{{ $module->module }}')">
                                    <i class='bx bxs-brightness'></i>
                                </a>
                            </td>
                            <td style="text-align: center">
                                <a href="#" onclick="moduleDelete({{$module->id_module}}, '{{$module->module}}', '{{route('modules.delete', ':id')}}')">
                                    <i class='bx bx-trash' style="color: red;"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Módulo</th>
                        <th>Descripción</th>
                        <th>Secuencia</th>
                        <th>Estatus</th>
                        <th>Cuadrante</th>
                        <th>Cursos</th>
                        <th>Pre</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @include('mgr.prerequisites_modal')
        @include('mgr.modalCopyElement')
    </div>
@endsection