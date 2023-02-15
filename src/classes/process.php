<?php
// display errors in case there are any
ini_set('display_errors', 0);
error_reporting(E_ALL);

// import required CSS parser classes
use YAWK\changeCSS;

// import changeCSS webApp editor class
require_once 'changeCSSApp.class.php';

// new instance of changeCSSApp
$app = new changeCSS\changeCSSApp();
// process file upload and parse css
$cssDocument = $app->processFileUploadAndParseCSS();
// get all css properties into array
$properties = $app->getProperties($cssDocument);
// get filtered array of menu items
$menuItems = $app->filterMenuItems($cssDocument);
// load dynamic menu built from css file
$app->displayNavigation($menuItems, false);
// generate form
$app->generateCssUpdateForm($properties);

// debug purpose: print css array
// $app->testing();

// debug purpose: create static menu
// $menuItems = array('Body', 'Fonts' => array('h1', 'h2', 'h3'), 'Colors', 'Backgrounds', 'Borders', 'Margins', 'Paddings', 'Heights', 'Widths', 'Misc');







