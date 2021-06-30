Vue.directive('select2', {
    inserted(el) {
        $(el).on('select2:select', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event);
        });

        $(el).on('select2:unselect', () => {
            const event = new Event('change', { bubbles: true, cancelable: true })
            el.dispatchEvent(event)
        })
    },
});

var app = new Vue({
    el: '#appAssignmentsForm',
    data: {
        iAssignmentBy: 6,
        oData: oServerData,
        dtStart: moment().format('YYYY-MM-DD'),
        dtEnd: moment().add(7, 'days').format('YYYY-MM-DD'),
        kaId: 0,
        student: 0,
        job: 0,
        department: 0,
        branch: 0,
        company: 0,
        organization: 0,
        lAssignments: []
    },
    methods: {
        getStudents() {
            if (!this.validateControl()) {
                return false;
            }

            SGui.showWaiting(4000);

            axios
                .get(this.oData.studentsRoute, {
                    params: {
                        'assignment_by': this.iAssignmentBy,
                        'student': this.student,
                        'job': this.job,
                        'department': this.department,
                        'branch': this.branch,
                        'company': this.company,
                        'organization': this.organization,
                    }
                })
                .then(response => {
                    console.log(response);

                    let students = response.data;
                    this.lAssignments = [];

                    for (const student of students) {
                        let oAss = new Assignment();

                        oAss.dt_assignment = this.dtStart;
                        oAss.dt_end = this.dtEnd;
                        oAss.knowledge_area_id = this.kaId;
                        oAss.student_id = student.id;

                        oAss.auxName = student.num_employee + " - " + student.full_name

                        this.lAssignments.push(oAss);
                    }

                    $('#studentsModal').modal('show');
                })
                .catch(err => {
                    console.log(err);
                    SGui.showError(err);
                });
        },
        validateControl() {
            let isValid = document.querySelector('#createForm').reportValidity();

            if (!isValid) {
                return false
            }

            switch (this.iAssignmentBy) {
                case 6:
                    if (parseInt(this.student) == 0) {
                        SGui.showError("Debes seleccionar un estudiante.");
                        return false;
                    }

                    break;

                case 5:
                    if (parseInt(this.job) == 0) {
                        SGui.showError("Debes seleccionar un puesto.");
                        return false;
                    }

                    break;

                case 4:
                    if (parseInt(this.department) == 0) {
                        SGui.showError("Debes seleccionar un departamento.");
                        return false;
                    }

                    break;

                case 3:
                    if (parseInt(this.branch) == 0) {
                        SGui.showError("Debes seleccionar una sucursal.");
                        return false;
                    }

                    break;

                case 2:
                    if (parseInt(this.company) == 0) {
                        SGui.showError("Debes seleccionar una empresa.");
                        return false;
                    }

                    break;

                case 1:
                    if (parseInt(this.organization) == 0) {
                        SGui.showError("Debes seleccionar una organización.");
                        return false;
                    }

                    break;

                default:
                    break;
            }

            return true;
        },
        assignArea() {
            /**
             * Petición al Controlador
             */
            axios.post(this.oData.storeRoute, {
                    'dt_start': this.dtStart,
                    'dt_end': this.dtEnd,
                    'assignment_by': this.iAssignmentBy,
                    'student': this.student,
                    'job': this.job,
                    'department': this.department,
                    'branch': this.branch,
                    'company': this.company,
                    'organization': this.organization,
                    'ka_id': this.kaId,
                    'assignments': JSON.stringify(this.lAssignments)
                })
                .then(response => {
                    let res = response.data;

                    $('#studentsModal').modal('hide');

                    SGui.showOk();
                    window.location.replace(this.oData.indexRoute);
                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        }
    },
})