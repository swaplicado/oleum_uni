@extends('layouts.appuni')

@section('scripts_section')
    <script type="text/javascript">
         $(document).ready(function() {
            var oContentsTable = $('#contents_table').DataTable({
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

            $('.js-select2-class').select2();
        } );
    </script>
    <script type="text/javascript">
        function GlobalData () {
            this.lElementContents = <?php echo json_encode($lElementContents) ?>;
            this.lContents = <?php echo json_encode($lContents) ?>;
            this.sGetRoute = <?php echo json_encode(route($sGetRoute)) ?>;
            this.storeRoute = <?php echo json_encode(route($storeRoute, [$idTopic])) ?>;
            this.idTopic = <?php echo json_encode($idTopic) ?>;
            this.idSubtopic = <?php echo json_encode($idSubtopic) ?>;
        }

        var oServerData = new GlobalData();
    </script>

@endsection

@section('content')
    @section('content_title', $title.' ['.($oSubtopic->subtopic).']')
<div id="contentsApp">
    <button id="rightnew" v-on:click="createElementContent()" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </button>
    <div class="row">
        <div class="col-md-12">
            <table id="contents_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre de archivo</th>
                        <th>Tipo de archivo</th>
                        <th>Orden</th>
                        <th>Vista previa</th>
                    </tr>
                </thead>
                <tbody>                    
                        <tr v-for="elemContent in oData.lElementContents">
                            <td>@{{ elemContent.file_name }}</td>
                            <td>@{{ elemContent.file_type }}</td>
                            <td>@{{ elemContent.order }}</td>
                            <td style="text-align: center">
                                <button v-if="elemContent.file_type != 'link'" class="btn btn-info" 
                                        v-on:click="preview(elemContent.id_content, elemContent.file_type, elemContent.file_name)">
                                    Ver <i class='bx bxs-show'></i>
                                </button>
                                <a v-else class="btn btn-info" target="_blank" :href="elemContent.file_path">
                                    Ver <i class='bx bxs-show'></i>
                                </a>
                            </td>
                        </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Nombre de archivo</th>
                        <th>Tipo de archivo</th>
                        <th>Orden</th>
                        <th>Vista previa</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @include('mgr.topics.subtopics.modal')
    @include('mgr.contents.preview')
</div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/contents/ElementContent.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/contents/VueContents.js') }}"></script>
@endsection