@csrf
<div class="mb-3">
    <label for="name" class="form-label">Nombre del cuadrante</label>
    <input type="text" class="form-control" id="name" name="name" value="{{ isset($oKa) ? $oKa->knowledge_area : '' }}">
</div>
<div class="mb-3">
    <label for="sequence" class="form-label">Secuencia</label>
    <div>
        <select class="select2class form-control" name="sequence" style="width: 75%">
            @foreach ($sequences as $seq)
                <option value="{{ $seq->id_sequence }}" {{ isset($oKa) && $oKa->sequence_id == $seq->id_sequence ? "selected" : "" }}>{{ $seq->seq }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="mb-3">
    <label for="objectives" class="form-label">Objetivos</label>
    <textarea id="objectives" name="objectives" class="form-control" aria-label="With textarea">{{ isset($oKa) ? $oKa->objectives : '' }}</textarea>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea class="form-control" id="description" name="description" rows="15">{{ isset($oKa) ? $oKa->description : '' }}</textarea>
</div>
<div class="mb-3">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input" name="has_document" {{ isset($oKa) && $oKa->has_document ? 'checked' : '' }}>
    Genera certificado
  </label>
</div>