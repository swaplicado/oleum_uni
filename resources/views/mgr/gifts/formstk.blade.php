@csrf
<div class="row">
    <div class="col-12 col-md-6">
        <div class="mb-3">
            <label for="quantity" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
    </div>

    <div class="col-12 col-md-6">
        <div class="mb-3">
            <label for="mov_type_id" class="form-label">Tipo de movimiento</label>
            <select class="form-select" name="mov_type_id" aria-label="Seleccione movimiento" required>
                @foreach ($movTypes as $tstk)
                    <option value="{{ $tstk->id_mov_type }}">{{ $tstk->code.'-'.$tstk->movement_type }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<input type="hidden" value="{{ $idGift }}" name="gift_id">
<input type="hidden" value="{{ $movClass }}" name="mov_class">

<div class="row">
    <div class="col-12">
        <div class="mb-3">
            <label for="comments" class="form-label">Comentarios</label>
            <input type="text" class="form-control" id="comments" name="comments" required>
        </div>
    </div>
</div>