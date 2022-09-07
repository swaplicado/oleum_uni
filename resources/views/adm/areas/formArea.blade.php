@csrf
<div class="row">
    <div class="col-md-6">
        <label for="module" class="form-label">Nombre del área funcional:</label>
        <input type="text" class="form-control" id="area" name="area" value="{{ isset($oArea) ? $oArea->area : '' }}">
    </div>
    <div class="col-md-6">
        <label for="father_area" class="form-label">Área padre:</label>
        <div>
            <select class="select2class form-control" name="father_area">
                <option value="0">No aplica</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id_area }}" {{ isset($oArea) && $oArea->father_area_id == $area->id_area ? "selected" : "" }}>{{ $area->area }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <label for="supervisor" class="form-label">Supervisor:</label>
        <div>
            <select class="select2class form-control" name="supervisor">
                <option value=""></option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ isset($oArea) && $oArea->user == $user->id ? "selected" : "" }}>{{ $user->full_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<br>
<button type="submit" class="btn btn-primary" style="float: right;">Guardar</button>
