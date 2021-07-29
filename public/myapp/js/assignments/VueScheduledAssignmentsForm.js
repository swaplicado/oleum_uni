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
    el: '#scheduledAssignmentsApp',
    data: {
        iAssignmentBy: 4,
        dtStart: moment().format('YYYY-MM-DD'),
        dtEnd: moment().add(365, 'days').format('YYYY-MM-DD'),
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
        }
    },
})