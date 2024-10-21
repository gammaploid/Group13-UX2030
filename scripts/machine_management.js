document.addEventListener('DOMContentLoaded', function() {
    // Add any client-side JavaScript for machine management here
});
$(document).ready(function() {
    // Machine operational status switcher
    $('.op-status-switch').click(function() {
        var machineId = $(this).data('machine-id');
        var status = $(this).hasClass('dimmed')? 'non-operational' : 'operational';

        $.ajax({
            type: 'POST',
            url: 'update_machine_status.php',
            data: {machineId: machineId, status: status},
            success: function(data) {
                if (data == 'success') {
                    if (status == 'non-operational') {
                        $(this).removeClass('dimmed').text('Mark as Operational');
                    } else {
                        $(this).addClass('dimmed').text('Mark as Non-Operational');
                    }
                } else {
                    alert('Error updating machine status');
                }
            }
        });
    });
});
