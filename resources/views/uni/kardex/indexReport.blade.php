@extends('layouts.appuni')

@section('content')
@section('content_title', 'Generar reporte de resultados')
<form id="form_report" action="{{route('kardex.generateReport')}}" method="POST">
    @csrf
<div class="row" id="appReport">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tipo_elemento" class="form-label">Tipo de elemento:</label>
                    <select class="form-select" name="tipo_elemento" v-model="SelElement" v-on:change="element_type()" required>
                        <option value="competencia">Cuadrante</option>
                        <option value="modulo">Módulo</option>
                        <option value="curso">Curso</option>
                        <option value="tema">Tema</option>
                        <option value="subtema">Subtema</option>
                        <option value="todo">Todo</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="elemento" class="form-label">@{{SelElement}}:</label>
                    <select class="form-select" id="elemento" name="elemento" required>
                        <option v-for="element in lElement" :value="element.id">@{{ element.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="type_level" class="form-label">Por nivel:</label>
                    <select class="form-select" name="type_level" v-model="SelNivel" v-on:change="level_type()" required>
                        <option value="organizacion">Organización</option>
                        <option value="empresa">Empresa</option>
                        <option value="sucursal">Sucursal</option>
                        <option value="departamento">Departamento</option>
                        <option value="puesto">Puesto</option>
                        <option value="estudiante">Estudiante</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="level" class="form-label">@{{SelNivel}}:</label>
                    <select class="form-select" id="level" name="level" required>
                        <option v-for="level in lNivel" :value="level.id">@{{ level.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="periodo" class="form-label">Selecciona periodo:</label>
                    <div id="reportrange" name="periodo" class="form-select" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                        <input id = "calendarStart" type="hidden" name="calendarStart" value="">
                        <input id = "calendarEnd" type="hidden" name="calendarEnd" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<button id="save" type="submit" class="btn btn-success" style="float: right;">Generar</button>
</form>
@endsection

@section('scripts_section')
<script type="text/javascript">
    $(function() {
        var start = moment().subtract(29, 'days');
        var end = moment();
        var calendarStart = document.getElementById('calendarStart');
        var calendarEnd = document.getElementById('calendarEnd');
        function cb(start, end) {
            calendarStart.value = start.format('yyyy-MM-DD');
            calendarEnd.value = end.format('yyyy-MM-DD');
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
    
        cb(start, end);
    
    });
    </script>
@endsection

@section('bottom_scripts')
<script type="text/javascript">
    function GlobalData () {
        this.lAreas = <?php echo json_encode($lAreas) ?>;
        this.lModules = <?php echo json_encode($lModules) ?>;
        this.lCourses = <?php echo json_encode($lCourses) ?>;
        this.lTopics = <?php echo json_encode($lTopics) ?>;
        this.lSubtopics = <?php echo json_encode($lSubtopics) ?>;
        this.lOrganizations = <?php echo json_encode($lOrganizations) ?>;
        this.lCompany = <?php echo json_encode($lCompany) ?>;
        this.lBranches = <?php echo json_encode($lBranches) ?>;
        this.lDepartments = <?php echo json_encode($lDepartments) ?>;
        this.lJobs = <?php echo json_encode($lJobs) ?>;
        this.lStudent = <?php echo json_encode($lStudent) ?>;
    }
    
    var oServerData = new GlobalData();
    console.log(oServerData);
</script>
<script src="{{ asset('myapp/js/kardexReport/report.js') }}"></script>
@endsection