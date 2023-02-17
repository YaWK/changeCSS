$(document).ready(function (keyframes, options) {
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
                $('#introduction').hide();
                $('#jumbotron').slideUp(800);
                // insert the xhr response into the css editor
                $('#css-editor').html(response).fadeIn(800);
                // because of xhr we need to select all fields with jscolor data attribute
                if(document.querySelector(`[data-jscolor]`)){
                    document.querySelector(`[data-jscolor]`).innerHTML += '<input data-jscolor="{}">';
                    jscolor.install() // recognizes new inputs and installs jscolor on them
                }
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

    $(document).ready(function() {
        $(document).on('click', '.nav-item .nav-link', function(e) {
            console.log('click event triggered');
            var tabId = $(this).attr('href');
            var group = $(this).text();
            console.log("clicked: " + tabId + " " + group);
            var contentPane = tabId.replace('nav-tab-content-', 'nav-tab-content-box-content-');
            $(tabId).html('<p>Loading... <b>'+group+'</b></p>'); // Loading indicator
        });
    });

    $(document).ready(function() {
        $('#jumbotron').css('visibility', 'visible');
        $('#navbar').css('visibility', 'visible');
    });

    //
    // $(document).ready(function() {
    //     animateObjects();
    // });
    //
    // function setRandomColors(numElements) {
    //     for (var i = 1; i <= numElements; i++) {
    //         var hexCode = '#' + Math.floor(Math.random() * 16777215).toString(16);
    //         $('#colorObject' + i).text(hexCode);
    //     }
    // }
    //
    // setRandomColors(5);
    //
    // function animateObjects() {
    //     $('.object').each(function() {
    //         var object = $(this); // Store $(this) reference in a variable
    //         var objectWidth = object.outerWidth();
    //         var objectHeight = object.outerHeight();
    //         var windowWidth = $(window).width();
    //         var windowHeight = $(window).height();
    //
    //         // set initial position at top of viewport
    //         var startX = Math.floor(Math.random() * (windowWidth - objectWidth));
    //         var startY = -objectHeight;
    //
    //         // generate random x value
    //         var xDirection = Math.random() < 0.5 ? -1 : 1; // randomly select left or right direction
    //         var xDistance = Math.floor(Math.random() * (windowWidth - objectWidth)); // randomly select distance from left edge
    //         var x = xDirection * xDistance;
    //
    //         // generate random y value
    //         var y = Math.floor(Math.random() * ($(window).height() - objectHeight));
    //
    //         var duration = Math.floor(Math.random() * 20) + 20;
    //         var startTime = performance.now();
    //         var lastTime = startTime;
    //         var currentTime;
    //
    //         // use requestAnimationFrame to schedule animation
    //         function step() {
    //             currentTime = performance.now();
    //             var elapsed = currentTime - lastTime;
    //             var progress = Math.min(elapsed / (duration * 1000), 1);
    //
    //             var position = object.position(); // Use stored object variable instead of $(this)
    //             var newPosition = {
    //                 left: position.left + x * progress,
    //                 top: position.top + y * progress
    //             };
    //
    //             // check if object has reached the middle of the viewport
    //             if (newPosition.top >= windowHeight / 1.75 - objectHeight / 1.75) {
    //                 var fadeOutDuration = duration * 500 / 6;
    //
    //                 // fade out and re-generate the object after fade out is complete
    //                 object.fadeOut({
    //                     duration: fadeOutDuration,
    //                     queue: false,
    //                     complete: function() {
    //                         object.css({
    //                             left: Math.floor(Math.random() * (windowWidth - objectWidth)),
    //                             top: -objectHeight
    //                         }).fadeIn({
    //                             duration: fadeOutDuration,
    //                             queue: false
    //                         });
    //                     }
    //                 });
    //             } else {
    //                 object.css(newPosition); // Use stored object variable instead of $(this)
    //                 lastTime = currentTime;
    //                 requestAnimationFrame(step);
    //             }
    //         }
    //
    //         object.css({
    //             left: startX,
    //             top: startY
    //         }).fadeIn({
    //             duration: duration * 1000 / 2,
    //             queue: false
    //         });
    //
    //         requestAnimationFrame(step);
    //     });
    // }




    // // Handle update form submission (work in progress!)
    // var requestInProgress = false;
    // $(document).on('click', 'nav-item nav-link', function(e) {
    //     console.log("Clicked");
    //    // e.preventDefault();
    //     var tabId = $(this).attr('href');
    //     var group = $(this).text();
    //     alert("clicked: " + tabId + " " + group);
    //     var contentPane = tabId.replace('nav-tab-content-', 'nav-tab-content-box-content-');
    //
    //     $(tabId).html('<p>Loading... <b>'+group+'</b></p>'); // Loading indicator
    //
    //     // document.querySelector(`[data-jscolor]`).innerHTML += '<input data-jscolor="{}">';
    //     // jscolor.install() // recognizes new inputs and installs jscolor on them
    //     //
    //     // $.ajax({
    //     //     url: 'src/classes/tab.php',
    //     //     data: { tabId: tabId, group: group, action: 'getCSSNode' },
    //     //     success: function (response) {
    //     //         // $(tabId).html(response);
    //     //         requestInProgress = false;
    //     //
    //     //     },
    //     //     error: function (xhr, status, error) {
    //     //         alert("Error: " + error);
    //     //         requestInProgress = false;
    //     //     }
    //     // });
    //
    // });



});