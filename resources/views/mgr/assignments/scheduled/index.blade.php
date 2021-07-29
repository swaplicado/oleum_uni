@extends('layouts.appuni')

@include('mgr.assignments.scheduled.sectionjs')

@section('content')
    @section('content_title', $title)
    <div class="row">
        <div class="col-md-12">
            <div class="row align-items-center">
                <div class="col-8"></div>
                <div class="col-2">
                    <a class="btn btn-info" style="margin-right: -10%" href="{{ route('assignments.manual.schedule') }}">
                        Programar
                    </a>
                </div>
                <div class="col-2">
                    <a href="{{ route($newRoute) }}" class="btn btn-success">
                        Nuevo<i class='bx bx-plus'></i>
                    </a>
                </div>
            </div>
            <table id="assignments_table" class="display stripe hover row-border order-column" style="width:100%">
                <thead>
                    <tr>
                        <th>Competencia</th>
                        <th>Programado desde</th>
                        <th>Programado hasta</th>
                        <th>Organización</th>
                        <th>Empresa</th>
                        <th>Sucursal</th>
                        <th>Departamento</th>
                        <th>Puesto</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lScheduled as $oSch)
                        <tr>
                            <td>{{ $oSch->knowledge_area }}</td>
                            <td>{{ $oSch->dt_start }}</td>
                            <td>{{ $oSch->dt_end }}</td>
                            <td>{{ $oSch->organization == null ? "-" : $oSch->organization }}</td>
                            <td>{{ $oSch->company == null ? "-" : $oSch->company }}</td>
                            <td>{{ $oSch->branch == null ? "-" : $oSch->branch }}</td>
                            <td>{{ $oSch->department == null ? "-" : $oSch->department }}</td>
                            <td>{{ $oSch->job == null ? "-" : $oSch->job }}</td>
                            <td style="text-align: center">
                                <button class="btn btn-info">
                                    <i class='bx bxs-edit-alt'></i>
                                </button>
                                <button class="btn btn-danger">
                                    <i class='bx bx-x'></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th>Competencia</th>
                        <th>Programado desde</th>
                        <th>Programado hasta</th>
                        <th>Organización</th>
                        <th>Empresa</th>
                        <th>Sucursal</th>
                        <th>Departamento</th>
                        <th>Puesto</th>
                        <th>-</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection