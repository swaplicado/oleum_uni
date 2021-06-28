class SGui {
    constructor() {}

    static showWaiting(iTimer) {
        Swal.fire({
            title: 'Espere...',
            timer: iTimer,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    static showOk() {
        Swal.fire({
            title: '¡Realizado!',
            timer: 1500,
            icon: 'success'
        });
    }

    static showError(sError) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: sError,
        });
    }
}