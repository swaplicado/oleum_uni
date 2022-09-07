var app = new Vue({
    el: '#areasApp',
    data: {
        oData: oServerData,
    },
    methods: {
        deleteArea(area_id, area){
            Swal.fire({
                title: 'Desea eliminar?',
                text: area,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if (result.isConfirmed) {
                    var ruta = this.oData.deleteRoute;
                    ruta = ruta.replace(':id',area_id);

                    axios.delete(ruta, {
                        'area_id': area_id,
                    })
                    .then(response => {
                        let res = response.data;
                        if(res.success){
                            SGui.showOk();
                            location.reload();
                        } else {
                            SGui.showError(res.message);
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                        SGui.showError(error);
                    });
                }
            });
        },
    },
})