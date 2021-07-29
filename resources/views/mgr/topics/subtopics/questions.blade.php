@extends('layouts.appuni')

@section('scripts_section')
    <script type="text/javascript">
         $(document).ready(function() {
            var oContentsTable = $('#questions_table').DataTable({
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
            this.lQuestions = <?php echo json_encode($lQuestions) ?>;            
            this.storeRoute = <?php echo json_encode(route($storeRoute)) ?>;
            this.updateRoute = <?php echo json_encode(route($updateRoute)) ?>;
            this.sGetRoute = <?php echo json_encode(route($sGetQuestion)) ?>;
            this.idTopic = <?php echo json_encode($idTopic) ?>;
            this.idSubtopic = <?php echo json_encode($idSubtopic) ?>;
        }

        var oServerData = new GlobalData();
    </script>

@endsection

@section('content')
    @section('content_title', $title.' ['.($oSubtopic->subtopic).']')
<div id="appQuestions">
    <button id="rightnew" v-on:click="createQuestion()" class="btn btn-success">
        Nuevo<i class='bx bx-plus'></i>
    </button>
    <div class="row">
        <div class="col-md-12">
            <table id="questions_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Pregunta</th>
                        <th>Número de respuestas</th>
                        <th>Respuesta correcta</th>
                        <th>Ver respuestas</th>
                    </tr>
                </thead>
                <tbody>                    
                        <tr v-for="question in oData.lQuestions">
                            <td>@{{ question.question }}</td>
                            <td>@{{ question.number_answers }}</td>
                            <td>@{{ question.answer_text }}</td>
                            <td style="text-align: center">
                                <button class="btn btn-info"
                                     v-on:click="viewAnswers(question.id_question, question.number_answers)">
                                    Ver respuestas<i class='bx bx-list-ol'></i></i>
                                </button>
                            </td>
                        </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Pregunta</th>
                        <th>Número de respuestas</th>
                        <th>Respuesta correcta</th>
                        <th>Ver respuestas</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @include('mgr.topics.subtopics.questionsmodal')
</div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript" src="{{ asset('myapp/js/questions/Question.js') }}"></script>
    <script type="text/javascript" src="{{ asset('myapp/js/questions/VueQuestions.js') }}"></script>
@endsection