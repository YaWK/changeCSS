$(document).ready(function() {
    // Handle file upload form submission
    $('#css-upload-form').submit(function(event) {
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'src/classes/process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#css-editor').html(response);
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });
    });

// Handle update form submission
    $('#css-editor').on('submit', 'form#css-update-form', function(event) {
        event.preventDefault();
        var formData = new FormData(this);
        formData.append('filename', $('input[name="filename"]').val());
        $.ajax({
            url: 'src/classes/update.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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