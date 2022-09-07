var app = new Vue({
    el: '#departmentsApp',
    data: {
        oData: oServerData,
        lAreas: oServerData.lAreas,
        selArea: null,
        idDept: 0,
        depto: '',
    },
    mounted(){
        let self = this;
        $('#selArea')
            .select2({
                placeholder: 'selecciona área',
                data: self.lAreas,
            })
            .on('select2:select', function (e){
                self.selArea = e.params.data.id;
            });
    },
    methods: {
        editArea(depto_id, depto, area_id) {
            this.idDept = depto_id;
            this.depto = depto;
            this.selArea = area_id;
            $('#selArea').val(area_id).trigger('change');
            $('#departmentArea').modal('show');
        },

        updateDeptoArea() {
            SGui.showWaiting(3000);

            axios.post(this.oData.routeUpDepto, {
                    'id_depto': this.idDept,
                    'area_id': this.selArea,
                })
                .then(response => {
                    let res = response.data;
                    if(res.success){
                        $('#departmentArea').modal('hide');
                        SGui.showOk();
                        location.reload();
                    }else{
                        SGui.showError(res.message);
                    }
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
    },
})