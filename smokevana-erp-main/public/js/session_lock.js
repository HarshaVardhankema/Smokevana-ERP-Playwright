$(document).ready(function () {

    let transaction_type = $('.modal_id').attr('transaction_type') ?? $('.edit_id').attr('transaction_type');
    let intervalId;
    let modal_id = $('.modal_id').attr('id');

    function checkModalLock() {
        $.ajax({
            url: `/session-lock/${transaction_type}/${modal_id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Lock Check Response:', response);
            },
            error: function (xhr, status, error) {
                console.error('Lock Check Error:', error);
            }
        });
    }

    function pingModalLock() {
        $.ajax({
            url: `/session-ping/${transaction_type}/${modal_id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    console.log('Ping Success:', response);
                } else {
                    swal("You can't edit this Invoice now", response.message, "error")
                        .then(function () {
                            $('.discount input').prop('disabled', true);
                            $('.discount select').prop('disabled', true);
                            $('.unit_price input').prop('disabled', true);
                            $('.quantity input').prop('disabled', true);
                            $('#openLock').removeClass('hide');
                            $('#openLock').text('🔒').prop('disabled', false);
                            $('#search_foot').addClass('hide');
                            $('.handle_lock').addClass('hide');
                            $('#save_button_invoice').addClass('hide');
                            $('#save_button_purchase').addClass('hide');
                        });
                }
            },
            error: function (xhr, status, error) {
                console.error('Ping Error:', error);
            }
        });
    }

    // Call this after loading modal
    function initModalLockChecker() {
        checkModalLock();
        intervalId = setInterval(function () {
            if ($('#openLock').is(':disabled')) {
                pingModalLock();
            }
        }, 58000);
    }


    // edit page logic

    let isLockedSession = true;

    function checkPageLock() {
        let transaction_id = $('.edit_id').attr('id')
        $.ajax({
            url: `/session-lock/${transaction_type}/${transaction_id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    isLockedSession = false;
                    console.log('Success:', response);
                } else {
                    isLockedSession = true;
                    console.log('Success:', response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    function pingPageLock() {
        let transaction_id = $('.edit_id').attr('id')
        $.ajax({
            url: `/session-ping/${transaction_type}/${transaction_id}`,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    console.log('Success:', response);
                } else {
                    swal("Session Expired", response.message, "error")
                        .then(function () {
                            window.location.reload();
                        });
                }

            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    setInterval(function () {
        if (!isLockedSession) {
            pingPageLock();
        }
    }, 58000);




    if (modal_id) {
        initModalLockChecker()
    } else {
        checkPageLock();
    }

    $(document).on('hidden.bs.modal', '.view_modal', function () {
        clearInterval(intervalId);
        $(this).empty();
    });




    // unlock button

    $("#openLock").on("click", function () {
        let url = `/session-unlock-model/${transaction_type}/${$(this).data('href')}`;
        let modalId = 'modal-' + new Date().getTime();
        $.ajax({
            url: url,
            success: function (response) {
                let newModal = $('<div class="modal fade" id="' + modalId +
                    '" data-backdrop="static" data-keyboard="false">' +
                    +
                    '<div class="modal-content">' + response + +
                    '</div>' +
                    '</div>');
                $('body').append(newModal);
                newModal.modal('show');
                newModal.on('hidden.bs.modal', function () {
                    $(this).remove();
                });
            }
        });

    });
})
