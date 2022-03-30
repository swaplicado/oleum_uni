@extends('layouts.appuni')

@section('content')
@section('content_title', 'Generar reporte de resultados')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                  <label for="">Tipo de elemento:</label>
                  <select class="form-control" name="" id="">
                    <option value="competencia">Área de competencia</option>
                    <option value="modulo">Módulo</option>
                    <option value="curso">Curso</option>
                    <option value="tema">Tema</option>
                    <option value="subtema">Subtema</option>
                    <option value="todo">Todo</option>
                  </select>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                  <label for=""></label>
                  <select class="form-control" name="" id="">
                    <option></option>
                    <option></option>
                    <option></option>
                  </select>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                  <label for="">Por nivel:</label>
                  <select class="form-control" name="" id="">
                    <option value="organizacion">Organización</option>
                    <option value="empresa">Empresa</option>
                    <option value="sucursal">Sucursal</option>
                    <option value="departamento">Departamento</option>
                    <option value="puesto">Puesto</option>
                    <option value="estudiante">Estudiante</option>
                  </select>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <label for="">Selecciona periodo:</label>
                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts_section')
<script type="text/javascript">
    $(function() {
        var start = moment().subtract(29, 'days');
        var end = moment();
    
        function cb(start, end) {
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