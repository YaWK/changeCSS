$(document).ready(function() {
    // Handle file upload form submission
    $('#css-upload-form').submit(function(event) {
        // Prevent default form submission behavior
        event.preventDefault();
        // Get the form data and create a new FormData object
        var formData = new FormData($(this)[0]);
        // Send an AJAX request to the process.php script
        $.ajax({
            // location of the script that handles the file and parse the document
            url: 'src/classes/process.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            cache: false,
            // If the request is successful, display the CSS editor
            success: function(response)
            {   // Some Animations
                // hide the upload form, logo and intro text; show the css editor
                $('#firstLine').fadeOut(200);
                $('#introduction').fadeOut(200);
                $('#logo').slideUp(600)
                $('#leadText').slideUp(680);
                $('#css-upload-form').slideUp(600);
                // insert the xhr response into the css editor
                $('#css-editor').html(response).fadeIn(800);
                // because of xhr we need to select all fields with jscolor data attribute
                document.querySelector(`[data-jscolor]`).innerHTML += '<input data-jscolor="{}">';
                // install jscolor on the new inputs
                jscolor.install() // recognizes new inputs and installs jscolor on them
            },
            // If the request fails, display an error message
            error: function(xhr, status, error)
            {   // todo: this should not only be a console log Handle AJAX errors properly
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
            // location of the script that handles the update / download of the document
            url: 'src/classes/update.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            // If the request is successful, create a download link for the modified CSS file
            success: function(response) {
                // Create a download link for the modified CSS file
                var a = document.createElement('a');
                // Create a URL object from the response
                var url = window.URL.createObjectURL(new Blob([response]));
                // Set the link's href to the URL object
                a.href = url;
                // Set the link's download attribute to the filename
                a.download = $('input[name="filename"]').val();
                // Add the link to the page and trigger the download
                $('body').append(a);
                // Simulate click to trigger the download
                a.click();
                // Clean up the link and URL object
                window.URL.revokeObjectURL(url);
                // Remove the link from the page
                $(a).remove();
            },
            // If the request fails, display an error message
            error: function(xhr, status, error)
            {   // Handle AJAX errors
                console.log('Error: ' + error);
            }
        });
    });

    // Handle update form submission (work in progress!)
    var requestInProgress = false;
    $(document).on('click', '.nav-item.nav-link', function(e) {
        e.preventDefault();
        var tabId = $(this).attr('href');
        var group = $(this).text();
//        var contentPane = tabId.replace('nav-tab-content-', 'nav-tab-content-box-content-');

        $(tabId).html('<p>Loading... <b>'+group+'</b></p>'); // Loading indicator

        document.querySelector(`[data-jscolor]`).innerHTML += '<input data-jscolor="{}">';
        jscolor.install() // recognizes new inputs and installs jscolor on them

        $.ajax({
            url: 'src/classes/tab.php',
            data: { tabId: tabId, group: group, action: 'getCSSNode' },
            success: function (response) {
                // $(tabId).html(response);
                requestInProgress = false;

            },
            error: function (xhr, status, error) {
                alert("Error: " + error);
                requestInProgress = false;
            }
        });

    });



});