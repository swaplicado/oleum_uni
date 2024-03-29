@extends('layouts.appuni')

@section('content')
    @section('content_title', 'Avance general')

    <div class="container table-responsive">
        <table id="head_table" class="display" style="width:100%; display: none; font-size: 3.5mm;">
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Departamento</th>
                    <th>Cuadrante</th>
                    <th>Curso</th>
                    <th>Subtema</th>
                    <th style="min-width: 70px;">Fecha-hora</th>
                    <th>Veces cursado</th>
                    @for ($i = 0; $i < $max_questions; $i++)
                        <th style="min-width: 70px;">Pregunta {{$i + 1}}</th>
                        <th style="min-width: 80px;">Resultado {{$i + 1}}</th>
                    @endfor
                    <th>Calif. final</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($areas as $area)
                    @foreach ($area->modules as $module)
                        @foreach ($module->courses as $course)
                            @foreach ($course->topics as $topic)
                                @foreach ($topic->subtopics as $subtopic)
                                    <tr>
                                        <td style="text-align: center;">{{$area->student_name}}</td>
                                        <td style="text-align: center;">{{$area->student_department}}</td>
                                        <td style="text-align: center;">{{$area->knowledge_area}}</td>
                                        <td style="text-align: center;">{{$course->course}}</td>
                                        <td style="text-align: center;">{{$subtopic->subtopic}}</td>
                                        @if(!is_null($subtopic->exam))
                                            <td style="text-align: center;">{{$subtopic->exam->date_taked}}</td>
                                            <td style="text-align: center;">{{$subtopic->exam->num_taked}}</td>
                                            @for ($i = 0; $i < $max_questions; $i++)
                                                @if(isset($subtopic->exam->takedQuestions[$i]))
                                                    <td style="text-align: center;">{{$subtopic->exam->takedQuestions[$i]['question']}}</td>
                                                    @if ($subtopic->exam->takedQuestions[$i]['is_correct'] == 1)
                                                        <td style="text-align: center;"><span style="display: none;">Correcto</span><span class="bx bx-check bx-md" style="color: green;"></span></td>
                                                    @else
                                                        <td style="text-align: center;"><span style="display: none;">Incorrecto</span><span class="bx bx-x bx-md" style="color: red;"></span></td>
                                                    @endif
                                                @else
                                                    <td style="text-align: center;">N\A</td>
                                                    <td style="text-align: center;">N\A</td>
                                                @endif
                                            @endfor
                                            <td style="text-align: center;">{{$subtopic->exam->grade}}</td>
                                        @else
                                            <td style="text-align: center;">N\A</td>
                                            <td style="text-align: center;">N\A</td>
                                            @for ($i = 0; $i < $max_questions; $i++)
                                                <td style="text-align: center;">N\A</td>
                                                <td style="text-align: center;">N\A</td>
                                            @endfor
                                            <td style="text-align: center;">N/A</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('bottom_scripts')
    <script type="text/javascript">
    $(document).ready(function() {
        table = $('#head_table').DataTable({
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
                ],  
            "initComplete": function(){ 
                $("#head_table").show(); 
            }
        });
    } );
</script>
@endsection