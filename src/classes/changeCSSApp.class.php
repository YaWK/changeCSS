<?php
// set namespace
namespace YAWK\changeCSS;
// import required CSS parser classes
use Sabberworm\CSS\CSSList\CSSList;
use Sabberworm\CSS\CSSList\Document;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Parsing\SourceException;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Value\Color;
use Sabberworm\CSS\Value\CSSFunction;
use Sabberworm\CSS\Value\URL;

// changeCSSApp class
// contains all methods to parse and edit css files
class changeCSSApp
{
    // class properties
    public string $filename;                                // file name of css file to be parsed
    // file object
    public string $file;                                    // file (upload) object of css file to be parsed
    private string $sabberwormPath = '../../assets/lib/';   // path to sabberworm css parser

    public function __construct()
    {
        // autoloader to include required parser assets
        spl_autoload_register(function ($class)
        {   // Replace backslashes in class name with forward slashes for file path
            $file = $this->sabberwormPath.str_replace('\\', '/', $class) . '.php';
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

            // try to parse css file
            try
            {   // parsing succeeded, stored in css document object
                $cssDocument = $parser->parse();
            }
            // catch parsing errors
            catch (SourceException $e)
            {   // parsing failed, exit with error message
                echo 'There was an error parsing the given CSS file: ' . isset($this->filename) . ' at line ' . $e->getLine() . ': ' . $e->getMessage();
            }
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
        // return false if no css document object was created
        return false;
    }

    // get all properties from css document object
    public function getProperties($cssDocument): array|bool
    {
        if (empty($cssDocument)){
            return false;
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

    // use this method to test things out
    public function testing(): void
    {   // exchange this with your own html markup
        echo'hello world';
    }

    // generate html markup for css properties
    function generateNavigation($tabElements, $parentTabCount = 0): string
    {   // create navigation tab markup
        $output = '<nav><div class="nav nav-tabs" id="nav-tab" role="tablist">';
        // init tab counter
        $tabCount = 0;
        // init first tab active flag as boolean
        $firstTabActive = true;
        // loop through all tab elements
        foreach ($tabElements as $key => $element) {
            // init active class
            $activeClass = '';
            // check if first tab is active
            if ($firstTabActive)
            {   // set active class
                $activeClass = ' active';
                $firstTabActive = false;
            }
            // check if element is an array
            if (is_array($element))
            {   // element is an array, so it contains sub elements
                $output .= '<a class="nav-item nav-link' . $activeClass . '" id="nav-tab-' . $parentTabCount . '-' . $tabCount . '" data-toggle="tab" href="#nav-tab-content-' . $parentTabCount . '-' . $tabCount . '" role="tab" aria-controls="nav-tab-' . $parentTabCount . '-' . $tabCount . '" aria-selected="false">' . $key . '</a>';
            } else
            {   // element is not an array, so it is a single element
                $output .= '<a class="nav-item nav-link' . $activeClass . '" id="nav-tab-' . $parentTabCount . '-' . $tabCount . '" data-toggle="tab" href="#nav-tab-content-' . $parentTabCount . '-' . $tabCount . '" role="tab" aria-controls="nav-tab-' . $parentTabCount . '-' . $tabCount . '" aria-selected="false">' . $element . '</a>';
            }
            // increment tab counter
            $tabCount++;
        }
        // close navigation tab markup
        $output .= '</div></nav><div class="tab-content" id="nav-tabContent">';
        // reset tab counter
        $tabCount = 0;
        // reset first tab active flag, will be used to set the first tab content pane to active
        $firstTabActive = true;
        // loop through all tab elements
        foreach ($tabElements as $key => $element)
        {   // init active class
            $activeClass = '';
            // check if first content pane is active
            if ($firstTabActive)
            {   // set active class
                $activeClass = ' show active';
                $firstTabActive = false;
            }
            // check if element is an array
            if (is_array($element))
            {   // element is an array, so it contains sub elements, display content pane sub elements
                $output .= '<div class="tab-pane fade' . $activeClass . '" id="nav-tab-content-' . $parentTabCount . '-' . $tabCount . '" role="tabpanel" aria-labelledby="nav-tab-' . $parentTabCount . '-' . $tabCount . '">';
                $output .= $this->generateNavigation($element, $parentTabCount . '-' . $tabCount);
                $output .= '</div>';
                $output .= '<div id="nav-tab-content-box-content-' . $parentTabCount . '-' . $tabCount . '"></div>';
            }
            else
            {   // element is not an array, so it is a single element, display content pane single element
                $output .= '<div class="tab-pane fade' . $activeClass . '" id="nav-tab-content-' . $parentTabCount . '-' . $tabCount . '" role="tabpanel" aria-labelledby="nav-tab-' . $parentTabCount . '-' . $tabCount . '">';
                $output .= '<p>Content for ' . $element . '</p></div>';
                $output .= '<div id="nav-tab-content-box-content-' . $parentTabCount . '-' . $tabCount . '"></div>';
            }
            // increment tab counter
            $tabCount++;
        }
        // close content pane markup
        $output .= '</div>';
        // return generated markup
        return $output;
    }

    // display navigation is a wrapper function for generateNavigation to echo the generated markup
    public function displayNavigation($tabElements): void
    {   // check if properties is an array
        echo "<pre>";
        print_r($tabElements);
        echo "</pre>";


        if (!is_array($tabElements) || (empty($tabElements)))
        {   // not an array or empty, so return
            return; // no properties to display
        }
        // generate and output the navigation markup
        echo $this->generateNavigation($tabElements);
    }

    // generate html markup for css properties
    public function generateCssUpdateForm($properties): void
    {
        // build form fields for each css property
        echo '<form id="css-update-form" method="POST">
                <button type="submit" class="btn-primary">Save</button>';
        if(!empty($this->filename)){
            echo '<p>processed: '.$this->filename.'</p>';
        }
        else {
            return; // exit function
        }
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
        echo '<input type="hidden" name="filename" value="'.$this->filename.'">
        </form>';
    }

    // display css properties is a wrapper function for generateCssUpdateForm to echo the generated markup
    // public function displayCssUpdateForm($properties): void

    // public function filterMenuItems($document) is a function to filter the css selectors and get menu items from it
    public function filterMenuItems($document): array
    {
        // Extract the selectors, properties, and values from the document
        $rules = array_filter($document->getAllRuleSets(), function($ruleSet) {
            return count($ruleSet->getRules()) > 0;
        });

        // init menu items array
        $cssArray = array_map(function($ruleSet)
        {   // check if ruleSet is an instance of DeclarationBlock
            if ($ruleSet instanceof DeclarationBlock)
            {   // get the selectors
                $selectors = array_map(function($selector) {
                    return $selector->getSelector();
                }, $ruleSet->getSelectors());

                // get the properties and values for each selector
                $properties = array_map(function($property)
                {
                    // Get the name and value of the property
                    $propertyName = $property->getRule();
                    $propertyValue = $property->getValue();

                    // Check if the value is a color and convert it to a hex code
                    if ($propertyValue instanceof Sabberworm\CSS\Value\Color) {
                        // Get the color value as a string
                        $colorValue = (string) $propertyValue;

                        // Parse the string to extract the hex code
                        if (preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $colorValue))
                        {   // The color value is already a hex code
                            $hexCode = $colorValue;
                        }
                        // check if color value is rgb (which is default for sabberworm parsed css)
                        elseif (preg_match('/^rgb\((\d+), (\d+), (\d+)\)$/', $colorValue, $matches))
                        {
                            // The color value is an RGB value
                            $r = dechex($matches[1]);
                            $g = dechex($matches[2]);
                            $b = dechex($matches[3]);

                            // Convert the RGB value to a hex code
                            $hexCode = sprintf("#%02s%02s%02s", $r, $g, $b);
                        }
                        else
                        {   // The color value is not a recognized format
                            $hexCode = '';
                            // set rgb as value
                            $hexCode = $propertyValue;
                        }

                        // Set the property value to the hex code
                        $propertyValue = $hexCode;
                    }

                    // extract size values to shrink the array
                    elseif ($propertyValue instanceof Sabberworm\CSS\Value\Size) {

                        // Get the size value as a string
                        $propertyValue = (string)$propertyValue;

                        // Parse the string to extract the size value
                        if (preg_match('/^(-?[0-9\.]+)(px|em|rem|ex|ch|vw|vh|vmin|vmax|%|)$/', $propertyValue, $matches)) {
                            // The size value is in a recognized format
                            $size = $matches[1].$matches[2];
                        } else {
                            // The size value is not a recognized format
                            $size = $propertyValue;
                        }
                        $propertyValue = $size;
                    }

                    return [
                        'name' => $propertyName,
                        'value' => $propertyValue,
                    ];
                }, $ruleSet->getRules());

                return [
                    'selector' => implode(', ', $selectors),
                    'properties' => $properties,
                ];
            }

            return;
        }, $rules);


        // Define an array of selectors and their corresponding categories
        // Loop over the categories and search for matching selectors
        $menuItems = array();
        // Define the mapping array
        $mapping = [
            'Fonts' => [
                'Default' => ['.h1', '.h2', '.h3', '.h4', '.h5', '.h6', 'p', 'a'],
            ],
            'Navigation' => [
                'Default' => ['.nav'],
                'Navbar' => ['.navbar'],
                'Nav Tabs' => ['.nav-tabs'],
                'Nav Pills' => ['.nav-pills'],
            ],
            'Buttons' => [
                'Default' => ['.btn'],
                'Primary' => ['.btn-primary'],
                'Secondary' => ['.btn-secondary'],
                'Info' => ['.btn-info'],
                'Dark' => ['.btn-dark'],
                'Light' => ['.btn-light'],
                'Success' => ['.btn-success'],
                'Warning' => ['.btn-warning'],
                'Danger' => ['.btn-danger'],
            ],
            'Table' => [
                'Default' => ['.table'],
                'Dark' => ['.table-dark'],
                'Hover' => ['.table-hover'],
                'Responsive' => ['.table-responsive'],
            ],
            'Forms' => [
                'Default' => ['.form-control'],
                'Form Check' => ['.form-check'],
                'Form Inline' => ['.form-inline'],
                'Form Group' => ['.form-group'],
                'Validated' => ['.was-validated'],
                'Input Group' => ['.input-group'],
                'Checkbox' => ['.custom-checkbox'],
                'Radio' => ['.custom-radio'],
                'Select' => ['.custom-select'],
                'File' => ['.custom-file'],
                'Range' => ['.custom-range'],
            ],
            'Carousel' => [
                'Default' => ['.carousel'],
                'Item' => ['.carousel-item'],
                'Fade' => ['.carousel-fade'],
                'Control' => ['.carousel-control'],
                'Indicators' => ['.carousel-indicators'],
            ],
            'Alert' => [
                'Default' => ['.alert'],
                'Primary' => ['.alert-primary'],
                'Secondary' => ['.alert-secondary'],
                'Info' => ['.alert-info'],
                'Success' => ['.alert-success'],
                'Warning' => ['.alert-warning'],
                'Danger' => ['.alert-danger'],
            ],
            'List Group' => [
                'Default' => ['.list-group'],
                'Item' => ['.list-group-item'],
            ],
            'Modal' => [
                'Default' => ['.modal'],
                'Dialog' => ['.modal-dialog'],
                'Backdrop' => ['.modal-backdrop'],
                'Header' => ['.modal-header'],
                'Body' => ['.modal-body'],
                'Footer' => ['.modal-footer'],
            ],
        ];
        // Define the array of selectors
        $cssSelectors = array();
        // initialize the loop counter, will be used to add the comma
        $i = 0;
        // Loop through the rules to extract the selectors that match the mapping
        foreach ($cssArray as $item) {
            // Check if the selector is set
            if (isset($item['selector']) && (!empty($item['selector'])))
                $selector = $item['selector'];
            else
                continue;

            // Check if the selector is in the mapping
            if ($i == 0) {
                $cssSelectors['selector'][] = $selector;
            } else {
                $cssSelectors['selector'][] .= ',' . $selector;
            }

            // Loop through the mapping to find the matching selectors
            foreach ($mapping as $category => $items)
            {   // Loop through the items in the category
                foreach ($items as $itemName => $itemSelectors)
                {   // Check if the selector is in the item selectors
                    if (in_array($selector, $itemSelectors))
                    {   // Add the selector to the menu items
                        $menuItems[$category][$itemName] = $selector;
                    }
                }
            }
            $i++;
        }
        // on update : return $cssSelectors; //  a list of the selectors
        // default: return the menu items
        return $menuItems;
    }
}