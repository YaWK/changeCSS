
<hr>
<h1>changeCSS</h1>
+++ NOT FOR USE YET +++ <b>WORK IN PROGRESS!</b>
<hr>

<b>When it's done, changeCSS will allow to literally load any css file from any random CSS framework (like e.g. Bootstrap or Tailwind) and helps changing even big css files easily and user friendly.</b>
<h2>What is changeCSS?</h2>
The benefits of changeCSS will be:

- Easy and user-friendly way to modify CSS files
- Ability to load and modify CSS files from different CSS frameworks
- "On-the-fly" generation of fields in real time that correspond to the content of your uploaded CSS file
- Automatic overview generated based on the content of your CSS file
- Option to download the modified CSS file and use it in your project
- Time-saving, as you don't have to manually search through a large CSS file
- Future extensibility and feature expansion in the code.


<h2>How will it work when it's finished?</h2>
You get a beautiful web frontend, where you can upload your desired css file. The file gets processed, and a set of fields corresponding to your uploaded file will be generated "on the fly" in real time. All values of this fields can be changed easily via a user-friendly interface. The overview generates itself automatically, depending on the content of your css file. After you applied your desired changes, you can save and download the modified css file. You just need to replace the new css file with the old one in your project, refresh your browser and you will see your freshly updated design with the changes you made before with the help of changeCSS.

<h2>But why?</h2>
Because sometimes you just want to change a few things in a css file, but you don't want to go through the whole file and change it manually. This is where changeCSS comes in handy. It's a tool to make your life easier.

<h2>How is it built?</h2>
The project is built with PHP and Javascript. The PHP class is used to read the css file and generate the fields. The Javascript is used to generate the overview and to handle the user input.

<h2>What is the current state of the project?</h2>
The project is currently in a very early stage. It's just a test case scenario. The class is able to read a css file and generate the fields. The fields are generated in real time, depending on the content of the css file. The overview is generated automatically, depending on the content of the css file. The user input is handled and the values are stored in an array. Yet this is just an example. The codebase of this project will grow as functionality and features expands over time.


<h2>Demo</h2>
Just clone the repository, upload it to your favourite webserver and open index.php in your browser.

<h2>How to use the class?</h2>

<!-- language: php -->

```php
<?php
// include the class
require_once('changeCSS.class.php');

// create a new instance
$parser = new CssParser();

// read the css file
$result = $parser->read('yourfile.css');

// show the data
$parser->showData($result, ".btn");
```
<hr>

Please consider: Right now, this is just a test-case-scenario. You can use this example with your own css file to play around and see how it works.
Project will grow as time goes by.

