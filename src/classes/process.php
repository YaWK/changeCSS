<?php
// display errors in case there are any
ini_set('display_errors', 1);
error_reporting(E_ALL);

// import required CSS parser classes
use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Value\Color;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\URL;

// autoloader function to include necessary src
function autoload($class): void
{   // Replace backslashes in class name with forward slashes for file path
    $file = '../../assets/lib/'.str_replace('\\', '/', $class) . '.php';
    require_once $file;
}

// register autoloader function
spl_autoload_register('autoload');

// Check if file was uploaded
if(isset($_FILES['css-file']) && $_FILES['css-file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['css-file']['tmp_name'];
    $filename = $_FILES['css-file']['name'];

    $parser = new Parser(file_get_contents($file));
    try {   // parse the file
        $cssDocument = $parser->parse();
    }
    catch (SourceException $e) {
        die('There was an error parsing the given CSS file: '.$filename.' at line '.$e->getLine().': '.$e->getMessage());
    }

    // init array to store CSS properties
    $properties = array();

    // loop through all declaration blocks in the parsed CSS document
    foreach ($cssDocument->getAllDeclarationBlocks() as $declarationBlock)
    {   // Get selectors and rules for each declaration block
        $selectors = $declarationBlock->getSelectors();
        $rules = $declarationBlock->getRules();

        // Loop through all selectors
        foreach ($selectors as $selector)
        {   // Convert the selector object to a string
            $selector = $selector->__toString();
            // Loop through all rules
            foreach ($rules as $rule)
            {   // Get the property name
                $property = $rule->getRule();
                // Get the value of the property
                $value = $rule->getValue();

                // Check the type of the value and handle it accordingly
                if ($value instanceof CSSList)
                {   // For CSSList value, implode the list components into a string
                    $value = implode(', ', $value->getListComponents());
                }
                else if ($value instanceof CSSFunction)
                {   // For CSSFunction value, create a string representation of the function
                    $value = '(' . implode(', ', $value->getArguments()) . ')';
                }
                else if ($value instanceof URL)
                {   // For URL value, convert it to a string
                    $value = $value->__toString();
                }
                else if ($value instanceof Color)
                {   // For Color value, convert it to a string
                    $value = $value->__toString();
                }
                else
                {   // For other value types, convert it to a string
                    $value = (string) $rule->getValue();
                }

                // Fill the $properties array with the selector, property, and value
                $properties[$selector][$property] = $value;
            }
        }
    }

    // build form fields for each css property
    echo '<form id="css-update-form" method="POST">';
    echo '<p>processed: '.$filename.'</p>';
    foreach ($properties as $selector => $data){
        if (is_array($data)){
            echo '<h2>'.$selector.'</h2><hr>';
            foreach ($data as $property => $value){
  //              if ($data == "color")
//                    $datavalue = sprintf("#%02x%02x%02x", $datavalue);
                echo '<label for="$property"><b class="text-muted">'.$property.'</b> <input type="text" id="'.$property.'" name="'.$property.'" data-jscolor="{previewSize:182, borderRadius:6, padding:0, sliderSize:110, 
    shadowColor:\'rgba(0,0,0,0.15)\'}" class="form-control" value="'.$value.'" style="width: 300px;"></label><br>';
            }
        echo '<br><br>';
        }
    }
    echo '<input type="hidden" name="filename" value="'.$filename.'">
        <button type="submit">Save</button>
        </form>';

}
else
{
    echo 'File upload failed';
}