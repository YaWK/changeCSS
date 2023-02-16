
<hr>
<h1>changeCSS</h1>
+++ NOT FOR USE YET +++ <b>WORK IN PROGRESS!</b>
<hr>

<b>When it's done, changeCSS will allow to literally load any css file from any random CSS framework (like e.g. Bootstrap) and helps changing even big css files easily and user friendly.</b>
<h2>Key Benefits</h2>
The benefits of changeCSS will be:

- Easy and user-friendly way to modify CSS files
- Ability to load and modify CSS files from different CSS frameworks or even your own custom CSS file
- "On-the-fly" generation of fields in real time that correspond to the content of your uploaded CSS file
- Automatic overview generated based on the content of your CSS file
- Option to download the modified CSS file and use it in your project
- Time-saving, as you don't have to manually search through a large CSS file
- Future extensibility and feature expansion in the code.


<h2>How will it work when it's finished?</h2>
<b>Load CSS, change CSS, download CSS. That's it.</b>

You get a clean web frontend, where you can upload your desired css file. The file gets processed, and a set of fields corresponding to your uploaded file will be generated "on the fly" in real time. All values of this fields can be changed easily via a user-friendly interface. The overview generates itself automatically, depending on the content of your css file. After you applied your desired changes, you can save and download the modified css file. You just need to replace the new css file with the old one in your project, refresh your browser and you will see your freshly updated design with the changes you made before with the help of changeCSS.

<h2>But why?</h2>
Because sometimes you just want to change a few things in a css file, but you don't want to go through the whole file and change it manually. This is where changeCSS comes in handy. It's a tool to make your life easier.

<h2>How is it built?</h2>
The project is built with PHP and Javascript. The PHP class is used to read the css file and generate the fields. The Javascript is used to generate the overview and to handle the user input.

<h2>What is the current state of the project?</h2>
The project is currently in a very early stage. It's just a test case scenario. The class is able to read a css file and generate the fields. The fields are generated in real time, depending on the content of the css file. The overview is generated automatically, depending on the content of the css file. The user input is handled and the values are stored in an array. Yet this is just an example. The codebase of this project will grow as functionality and features expands over time.


<h2>Demo</h2>
Just clone the repository, upload it to your favourite webserver and open index.php in your browser.

<h2>How to use the class?</h2>
If you want to use this class "standalone" in your own project, there might be steps like:

<!-- language: php -->

```php
<?php
use YAWK\changeCSS;

// import changeCSS webApp editor class
require_once 'changeCSSApp.class.php';

// new instance of changeCSSApp
$app = new changeCSS\changeCSSApp();

// use method to load css file
$cssFile = $app->loadCSS("css/bootstrap.min.css"); // UPCOMING / WORK IN PROGRESS

// use method to process file upload and parse css
$cssDocument = $app->processAndParse($cssFile); // UPCOMING / WORK IN PROGRESS

// get all css properties from document object into array
$properties = $app->getProperties($cssDocument);

// get filtered array of menu items
$menuItems = $app->filterMenuItems($cssDocument, true, false);

// build menu from filtered menu items array
$app->displayNavigation($menuItems);

// if you want to get a list of all selectors, use this method
// $selectorList = $app->getSelectorList($cssDocument); // output it via var_dump($selectorList);

// generate form with fields corresponding to the css properties // UPCOMING / WORK IN PROGRESS
// $app->generateCssUpdateForm($properties);

// generate overview of css file // UPCOMING / WORK IN PROGRESS
// $app->generateOverview($cssDocument);

// generate css string / file from array // UPCOMING / WORK IN PROGRESS
// $app->generateCssFile($properties); // output it via var_dump($app->generateCssFile($properties));

// more methods to come...

```
<hr>

Please consider: Right now, this is just a test-case-scenario. You can use this example with your own css file to play around and see how it works.
Project will grow as time goes by.

