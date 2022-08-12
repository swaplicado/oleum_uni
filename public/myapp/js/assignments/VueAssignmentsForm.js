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
        lAssignments: [],
        lKAreas: oServerData.lKAreas,
        lAssignmentsBy: oServerData.lAssignBy,
        lStudents: oServerData.lStudents,
        lJobs: oServerData.lJobs,
        lDepartments: oServerData.lDepartments,
        lBranches: oServerData.lBranches,
        lCompanies: oServerData.lCompanies,
        lOrganizations: oServerData.lOrganizations,
        type_sel: "Seleccione estudiante",
        durationDays: 0,
    },
    mounted() {
        let self = this;
        $('#selec_ka')
            .select2({
                placeholder: 'selecciona cuadrante',
                data: self.lKAreas,
            })
            .on('select2:select', function (e){
                self.kaId = e.params.data.id;
                self.getDurationDays(self.kaId);
            });
        $('#selec_iAssignmentBy')
            .select2({ 
                placeholder: 'selecciona',
                data: self.lAssignmentsBy,
            });
        $('#type_selec')
            .select2({ 
                placeholder: 'selecciona',
                data: self.lStudents,
            });
        $('#type_selec').on('select2:select', function (e) {
            switch (parseInt(self.iAssignmentBy)) {
                case 6:
                    self.student = e.params.data.id;
                    break;
                case 5:
                    self.job = e.params.data.id;
                    break;
                case 4:
                    self.department = e.params.data.id;
                    break;
                case 3:
                    self.branch = e.params.data.id;
                    break;
                case 2:
                    self.company = e.params.data.id;
                    break;
                case 1:
                    self.organization = e.params.data.id;
                    break;
            
                default:
                    break;
            }
        });
        $('#selec_iAssignmentBy').on('select2:select', function (e) {
            var data = e.params.data;
            self.iAssignmentBy = data.id;
            $('#type_selec').empty();
            switch (parseInt(self.iAssignmentBy)) {
                case 6:
                    self.type_sel = "Seleccione estudiante";
                    $('#type_selec')
                        .select2({ 
                            placeholder: 'Seleccione estudiante',
                            data: self.lStudents,
                        })
                    break;
                case 5:
                    self.type_sel = "Seleccione puesto";
                    $('#type_selec')
                        .select2({ 
                            placeholder: 'Seleccione puesto',
                            data: self.lJobs,
                        })
                    break;
                case 4:
                    self.type_sel = "Seleccione departamento";
                    $('#type_selec')
                        .select2({ 
                            placeholder: 'selecciona',
                            data: self.lDepartments,
                        })
                    break;
                case 3:
                    self.type_sel = "Seleccione sucursal";
                    $('#type_selec')
                        .select2({ 
                            placeholder: 'selecciona',
                            data: self.lBranches,
                        })
                    break;
                case 2:
                    self.type_sel = "Seleccione empresa";
                    $('#type_selec')
                        .select2({ 
                            placeholder: 'selecciona',
                            data: self.lCompanies,
                        })
                    break;
                case 1:
                    self.type_sel = "Seleccione organización";
                    $('#type_selec')
                        .select2({ 
                            placeholder: 'selecciona',
                            data: self.lOrganizations,
                        })
                    break;
                default:
                    break;
            }
        });

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

                        oAss.auxName = student.num_employee + " - " + student.full_name;

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
        discardAssignment(assignment) {
            this.lAssignments.splice(this.lAssignments.indexOf(assignment), 1);
            SGui.showOk();
        },
        assignArea() {
            SGui.showWaiting(4000);
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

                    if(res.success == undefined){
                        SGui.showOk();
                        window.location.replace(this.oData.indexRoute);
                    } else{
                        SGui.showMessage('Error',  res.message, res.icon);
                    }

                })
                .catch(function(error) {
                    console.log(error);
                    SGui.showError(error);
                });
        },

        getDurationDays(ka_id){
            axios.post(oServerData.durationRoute, {
                'ka': ka_id,
            })
            .then(response => {
                this.durationDays = response.data;
                this.setDurationDays();
            })
            .catch(function(error) {
                console.log(error);
            });
        },

        setDurationDays(){
            this.dtEnd = moment(this.dtStart, 'YYYY-MM-DD').add(this.durationDays, 'days').format('YYYY-MM-DD');
        }
    },
})