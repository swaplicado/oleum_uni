@csrf
<div class="row">
    <div class="col-6">
        <label for="dt_assignment" class="form-label">Fecha inicio*:</label>
        <input type="date" class="form-control" id="dt_assignment" name="dt_assignment" v-model="dtStart" required>
    </div>
    <div class="col-6">
        <label for="dt_end" class="form-label">Fecha límite*:</label>
        <input type="date" class="form-control" id="dt_end" name="dt_end" v-model="dtEnd" required>
    </div>
</div>
<br>
<div class="mb-3">
    <label for="ka_id" class="form-label">Área de competencia*:</label>
    <div>
        <select class="select2class form-control" v-model="kaId" name="ka_id" style="width: 85%" required v-select2>
            @foreach ($lKAreas as $oKa)
                <option value="{{ $oKa->id_knowledge_area }}">{{ $oKa->knowledge_area }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mb-3">
    <label for="assignment_by" class="form-label">Asignar por*:</label>
    <div>
        <select class="select2class form-control" v-model="iAssignmentBy" name="assignment_by" style="width: 85%" required v-select2>
            @foreach ($lAssignBy as $assBy)
                <option value="{{ $assBy->id }}">{{ $assBy->text }}</option>
            @endforeach
        </select>
    </div>
</div>
<div v-if="iAssignmentBy == 6" class="mb-3">
    <label for="student" class="form-label">Seleccione estudiante*:</label>
    <div>
        <select class="form-control" name="student" v-model="student" style="width: 85%">
            @foreach ($lStudents as $oStudent)
                <option value="{{ $oStudent->id }}">{{ $oStudent->num_employee.'-'.$oStudent->full_name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div v-else-if="iAssignmentBy == 5" class="mb-3">
    <label for="job" class="form-label">Seleccione puesto*:</label>
    <div>
        <select class="form-control" name="job" v-model="job" style="width: 85%">
            @foreach ($lJobs as $oJob)
                <option value="{{ $oJob->id_job }}">{{ $oJob->job }}</option>
            @endforeach
        </select>
    </div>
</div>
<div v-else-if="iAssignmentBy == 4" class="mb-3">
    <label for="department" class="form-label">Seleccione departamento*:</label>
    <div>
        <select class="form-control" name="department" v-model="department" style="width: 85%">
            @foreach ($lDepartments as $oDept)
                <option value="{{ $oDept->id_department }}">{{ $oDept->department }}</option>
            @endforeach
        </select>
    </div>
</div>
<div v-else-if="iAssignmentBy == 3" class="mb-3">
    <label for="branch" class="form-label">Seleccione sucursal*:</label>
    <div>
        <select class="form-control" name="branch" v-model="branch" style="width: 85%">
            @foreach ($lBranches as $oBranch)
                <option value="{{ $oBranch->id_branch }}">{{ $oBranch->branch }}</option>
            @endforeach
        </select>
    </div>
</div>
<div v-else-if="iAssignmentBy == 2" class="mb-3">
    <label for="company" class="form-label">Seleccione empresa*:</label>
    <div>
        <select class="form-control" name="company" v-model="company" style="width: 85%">
            @foreach ($lCompanies as $oCompany)
                <option value="{{ $oCompany->id_company }}">{{ $oCompany->company }}</option>
            @endforeach
        </select>
    </div>
</div>
<div v-else-if="iAssignmentBy == 1" class="mb-3">
    <label for="organization" class="form-label">Seleccione organización*:</label>
    <div>
        <select class="form-control" name="organization" v-model="organization" style="width: 85%">
            @foreach ($lOrganizations as $oOrg)
                <option value="{{ $oOrg->id_organization }}">{{ $oOrg->organization }}</option>
            @endforeach
        </select>
    </div>
</div>
