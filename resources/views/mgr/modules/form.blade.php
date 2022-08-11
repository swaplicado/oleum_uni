@csrf
<div class="mb-3">
    <label for="module" class="form-label">Nombre del módulo</label>
    <input type="text" class="form-control" id="module" name="module" value="{{ isset($oModule) ? $oModule->module : '' }}">
</div>
<div class="row">
    <div class="col-md-3">
        <label for="sequence" class="form-label">Secuencia</label>
        <div>
            <select class="select2class form-control" name="sequence">
                @foreach ($sequences as $seq)
                    <option value="{{ $seq->id_sequence }}" {{ isset($oModule) && $oModule->sequence_id == $seq->id_sequence ? "selected" : "" }}>{{ $seq->seq }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <label for="completion_days" class="form-label">Duración (días)</label>
        <input type="number" value="{{ old('completion_days', $oModule->completion_days ?? '') }}" class="form-control" id="completion_days" name="completion_days">
    </div>
    <div class="col-md-6">
        <label for="pre_module" class="form-label">Módulo anterior:</label>
        <select class="select2class form-control" name="pre_module">
            <option value="">Ninguno</option>
            @foreach ($lModules as $module)
                <option value="{{ $module->id_module }}" {{ isset($oModule) && $oModule->pre_module_id == $module->id_module ? "selected" : "" }}>{{ $module->module }}</option>
            @endforeach
        </select>
    </div>
</div>
<input type="hidden" name="ka_id" value="{{ isset($oModule) ? $oModule->knowledge_area_id : $kArea }}">
<div class="mb-3">
    <label for="objectives" class="form-label">Objetivos</label>
    <textarea id="objectives" name="objectives" class="form-control" aria-label="With textarea">{{ isset($oModule) ? $oModule->objectives : '' }}</textarea>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea id="description" name="description" class="form-control" rows="5" aria-label="With textarea">{{ isset($oModule) ? $oModule->description : '' }}</textarea>
</div>
<div class="mb-3">
    <label class="form-check-label">
      <input type="checkbox" class="form-check-input" name="has_document" {{ isset($oModule) && $oModule->has_document ? 'checked' : '' }}>
      Genera reconocimiento
    </label>
</div>
