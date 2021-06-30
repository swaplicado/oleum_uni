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

                    SGui.showOk();
                    location.reload();
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        }
    },
})