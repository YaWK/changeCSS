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

// create static menu
$tabElements = array('Body', 'Fonts' => array('h1', 'h2', 'h3'), 'Colors', 'Backgrounds', 'Borders', 'Margins', 'Paddings', 'Heights', 'Widths', 'Misc');
$app->displayNavigation($tabElements, false);

// generate form
$app->generateCssUpdateForm($properties);

// $app->testing();







