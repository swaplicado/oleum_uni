class SGui {
    constructor() {}

    showWaiting(iTimer) {
        Swal.fire({
            title: 'Espere...',
            timer: iTimer,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then((result) => {
            /* Read more about handling dismissals below */
        })
    }
}