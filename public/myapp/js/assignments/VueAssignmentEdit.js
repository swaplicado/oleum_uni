var app = new Vue({
    el: '#indexAssignmentsApp',
    data: {
        oData: oServerData,
        dtStart: moment().format('YYYY-MM-DD'),
        dtEnd: moment().format('YYYY-MM-DD'),
        student: "",
        idAssignment: 0
    },
    methods: {
        editAssignment(dtStart, dtEnd, student, idAssignment) {
            this.dtStart = dtStart;
            this.dtEnd = dtEnd;
            this.student = student;
            this.idAssignment = idAssignment;

            $('#editAssignmentModal').modal('show');
        },
        updateAssignment() {
            SGui.showWaiting(3000);
            /**
             * Petición al Controlador
             */
            axios.put(this.oData.updateRoute, {
                    'dt_assignment': this.dtStart,
                    'dt_end': this.dtEnd,
                    'id_assignment': this.idAssignment
                })
                .then(response => {
                    let res = response.data;

                    $('#editAssignmentModal').modal('hide');

                    if(res.success == undefined){
                        SGui.showOk();
                        location.reload();
                    } else{
                        SGui.showMessage('Error',  res.message, res.icon);
                    }

                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },
        deleteAssignment(id, route) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se eliminará la asignación de cuadrante.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, bórrala'
            }).then((result) => {
                if (result.isConfirmed) {
                    SGui.showWaiting(3000);

                    axios
                        .delete(route)
                        .then(response => {
                            SGui.showOk();
                            location.reload();
                        })
                        .catch(err => {
                            console.log(err);
                            SGui.showError(err);
                        });
                }
            })
        }
    },
})