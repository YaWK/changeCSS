<?php
// display errors in case there are any
ini_set('display_errors', 1);
error_reporting(E_ALL);

// import required CSS parser classes
use YAWK\changeCSS;

// import editor class
require_once 'changeCSSApp.class.php';

// new instance of changeCSSApp
$app = new changeCSS\changeCSSApp();
// process file upload and parse css
$cssDocument = $app->processFileUploadAndParseCSS();
// get all css properties into array
$properties = $app->getProperties($cssDocument);


    // build form fields for each css property
    echo '<form id="css-update-form" method="POST">';
    echo '<p>processed: '.$app->filename.'</p>';
    foreach ($properties as $selector => $data){
        if (is_array($data)){
            echo '<h2>'.$selector.'</h2><hr>';
            foreach ($data as $property => $value){


                if ($property == "color"
                || ($property == "background-color")
                || ($property == "border-color")) {
                    echo '<label for="$property"><b class="text-muted">'.$property.'</b> <input type="text" id="'.$property.'" name="'.$property.'" data-jscolor="{previewSize:182, borderRadius:6, padding:0, sliderSize:110, 
    shadowColor:\'rgba(0,0,0,0.15)\'}" class="form-control" value="'.$value.'" style="width: 300px;"></label><br>';
                }
                else {
                    echo '<label for="$property"><b class="text-muted">'.$property.'</b> <input type="text" id="'.$property.'" name="'.$property.'" class="form-control" value="'.$value.'" style="width: 300px;"></label><br>';
                }


            }
        echo '<br><br>';
        }
    }
    echo '<input type="hidden" name="filename" value="'.$app->filename.'">
        <button type="submit">Save</button>
        </form>';

//}
//else
//{
//    echo 'File upload failed';
//}