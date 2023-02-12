$(document).ready(function() {
    // Handle file upload form submission
    $('form').submit(function(event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'classes/process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('body').html(response);
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    });

    // Handle update form submission
    $('body').on('submit', 'form#update-form', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: 'classes/update.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(new Blob([response]));
                a.href = url;
                a.download = $('input[name="filename"]').val();
                $('body').append(a);
                a.click();
                window.URL.revokeObjectURL(url);
                $(a).remove();
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    });
});