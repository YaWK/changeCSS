<?php
// set namespace
namespace YAWK\changeCSS;
// import required CSS parser classes
use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\Value\Color;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\URL;

// changeCSSApp class
// contains all methods to parse and edit css files
class changeCSSApp
{
    // properties
    // filename of css file to parse
    public string $filename;
    // file object
    public string $file;

    public function __construct()
    {
        // autoloader to include required parser assets
        spl_autoload_register(function ($class) {
            $file = '../../assets/lib/'.str_replace('\\', '/', $class) . '.php';
            require_once $file;
        });
    }

    // parse css file that was uploaded before and return css document object
    public function processFileUploadAndParseCSS(): Document|bool
    {
        // Check if file was uploaded
        if (isset($_FILES['css-file']) && $_FILES['css-file']['error'] === UPLOAD_ERR_OK)
        {   // Get the file object
            $this->file = $_FILES['css-file']['tmp_name'];
            // Get the file name
            $this->filename = $_FILES['css-file']['name'];
            // Create a new instance of the CSS parser and parse the uploaded css file
            $parser = new Parser(file_get_contents($this->file));
            try {   // parsing succeeded, stored in css document object
                $cssDocument = $parser->parse();
            } catch (SourceException $e)
            {   // parsing failed, exit with error message
                echo 'There was an error parsing the given CSS file: ' . $this->filename . ' at line ' . $e->getLine() . ': ' . $e->getMessage();
            }
        }
        else {
            // if no file was uploaded, exit with error message
            echo 'No CSS file was uploaded.';
        }
        // check if document object was created
        if (!isset($cssDocument))
        {   // no css document object was created, exit with error message
            echo 'No CSS file was uploaded. No CSS document object was created. Please check your file upload. Upload Error Details: ' . $_FILES['css-file']['error'];
        }
        else
        {   // return css document object
            return $cssDocument;
        }
        return false;
    }

    // get all properties from css document object
    public function getProperties($cssDocument): array
    {
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
                        $value = implode(', ', $rule->getListComponents());
                    }
                    else if ($value instanceof Color)
                    {   // For Color value, convert it to a string
                        $value = $value->__toString();
                    }
                    else if ($value instanceof CSSFunction)
                    {   // For CSSFunction value, create a string representation of the function
                        $value = '(' . implode(', ', $value->getArguments()) . ')';
                    }
                    else if ($value instanceof URL)
                    {   // For URL value, convert it to a string
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
        return $properties;
    }


}