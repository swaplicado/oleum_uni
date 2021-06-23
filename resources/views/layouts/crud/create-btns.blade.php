<div class="row">
    @section('scripts_section_complement')
        <script>
            function onSave() {
                document.getElementById("saveButton").disabled = true;
                let oGui = new SGui();
                oGui.showWaiting(3000);
                document.getElementById("createForm").submit();
            }
        </script>
    @endsection
    <div class="col-md-2 offset-md-10">
        <button id="saveButton" onclick="onSave()"  class="btn btn-success">Guardar</button>
        <button type="reset" class="btn btn-danger">Cancelar</button>
    </div>
</div>