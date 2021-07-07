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

    static showMessage(sTitle, sMessage, sIcon = 0) {
        Swal.fire({
            icon: sIcon == 0 ? 'info' : sIcon,
            title: sTitle,
            text: sMessage
        })
    }
}