/**
 * Custom JavaScript for Raffle System
 */

$(document).ready(function() {
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-toggle="popover"]').popover();
    
    // Confirm delete actions
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item?')) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Form validation
    $('form').on('submit', function() {
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true);
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
    });
    
    // DataTables initialization (if available)
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }
    
    // Number formatting
    $('.format-money').each(function() {
        var value = parseFloat($(this).text());
        if (!isNaN(value)) {
            $(this).text('GHS ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        }
    });
    
    // Date picker initialization (if available)
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }
    
    // Select2 initialization (if available)
    if ($.fn.select2) {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }
    
    // AJAX form submission
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var originalText = $btn.html();
        
        $.ajax({
            url: $form.attr('action'),
            method: $form.attr('method'),
            data: $form.serialize(),
            beforeSend: function() {
                $btn.prop('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
    
});

// Helper function to format currency
function formatMoney(amount, currency = 'GHS') {
    return currency + ' ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Helper function to format date
function formatDate(date) {
    var d = new Date(date);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
