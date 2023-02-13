$(document).ready(function() {
    // Handle file upload form submission
    $('#css-upload-form').submit(function(event) {
        // Prevent default form submission behavior
        event.preventDefault();
        // Get the form data and create a new FormData object
        var formData = new FormData($(this)[0]);
        // Send an AJAX request to the process.php script
        $.ajax({
            url: 'src/classes/process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Update the CSS editor with the response HTML
                $('#css-editor').html(response);
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.log('Error: ' + error);
            }
        });
    });

    // Handle update form submission
    $('#css-editor').on('submit', 'form#css-update-form', function(event) {
        // Prevent default form submission behavior
        event.preventDefault();
        // Create a new FormData object from the form
        var formData = new FormData(this);
        // Add the filename input to the form data
        formData.append('filename', $('input[name="filename"]').val());
        // Send an AJAX request to the update.php script
        $.ajax({
            url: 'src/classes/update.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Create a download link for the modified CSS file
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(new Blob([response]));
                a.href = url;
                a.download = $('input[name="filename"]').val();
                // Add the link to the page and trigger the download
                $('body').append(a);
                a.click();
                // Clean up the link and URL object
                window.URL.revokeObjectURL(url);
                $(a).remove();
            },
            error: function(xhr, status, error) {
                // Handle AJAX errors
                console.log('Error: ' + error);
            }
        });
    });
});