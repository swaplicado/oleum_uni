@csrf
<div class="mb-3">
    <label for="module" class="form-label">Nombre del módulo</label>
    <input type="text" class="form-control" id="module" name="module">
</div>
<div class="mb-3">
    <label for="sequence" class="form-label">Secuencia</label>
    <div>
        <select class="select2class form-control" name="sequence" style="width: 75%">
            @foreach ($sequences as $seq)
                <option value="{{ $seq->id_sequence }}">{{ $seq->seq }}</option>
            @endforeach
        </select>
    </div>
</div>
<input type="hidden" name="ka_id" value="{{ $kArea }}">
<div class="mb-3">
    <label for="objectives" class="form-label">Objetivos</label>
    <textarea id="objectives" name="objectives" class="form-control" aria-label="With textarea"></textarea>
</div>
<div class="mb-3">
    <label for="description" class="form-label">Descripción</label>
    <textarea id="description" name="description" class="form-control" rows="5" aria-label="With textarea"></textarea>
</div>