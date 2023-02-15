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
    public function displayNavigation($tabElements, $grouped): void
    {   // check if properties is an array
        if (!is_array($tabElements) || (empty($tabElements)))
        {   // not an array or empty, so return
            return; // no properties to display
        }
        // check if output should be grouped
        if (isset($grouped) && $grouped == 'true')
        {   // display grouped navigation
            echo $this->generateGroupedNavigation($tabElements);
            return;
        }
        // generate and output the navigation markup
        echo $this->generateNavigation($tabElements);
    }

    // generate html markup for grouped css properties
    function generateGroupedNavigation($tabElements, $parentTabCount = 0): string
    {
        $groupedArray = $this->groupData($tabElements);

        $output = '<nav><div class="nav nav-tabs" id="nav-tab" role="tablist">';
        $tabCount = 0;
        $firstTabActive = true;
        foreach ($groupedArray as $key => $element) {
            $activeClass = '';
            if ($firstTabActive) {
                $activeClass = ' active';
                $firstTabActive = false;
            }
            $output .= '<a class="nav-item nav-link' . $activeClass . '" id="nav-tab-' . $parentTabCount . '-' . $tabCount . '" data-toggle="tab" href="#nav-tab-content-' . $parentTabCount . '-' . $tabCount . '" role="tab" aria-controls="nav-tab-' . $parentTabCount . '-' . $tabCount . '" aria-selected="false">' . $key . '</a>';
            $tabCount++;
        }
        $output .= '</div></nav><div class="tab-content" id="nav-tabContent">';
        $tabCount = 0;
        $firstTabActive = true;
        foreach ($groupedArray as $key => $element) {
            $activeClass = '';
            if ($firstTabActive) {
                $activeClass = ' show active';
                $firstTabActive = false;
            }
            $output .= '<div class="tab-pane fade' . $activeClass . '" id="nav-tab-content-' . $parentTabCount . '-' . $tabCount . '" role="tabpanel" aria-labelledby="nav-tab-' . $parentTabCount . '-' . $tabCount . '">';
            $output .= $this->generateNavigation($element, $parentTabCount . '-' . $tabCount);
            $output .= '</div>';
            $tabCount++;
        }
        $output .= '</div>';
        return $output;
    }

    public function groupData($cssArray): array
    {
        $groupedArray = array();
        foreach ($cssArray as $selector => $properties) {
//            if (str_starts_with($selector, ".pos-") || str_starts_with($selector, "pos-")) {
//                continue;
//            }
            $group = $this->getGroup($selector);
            if (!isset($groupedArray[$group])) {
                $groupedArray[$group] = array();
            }
            $groupedArray[$group][$selector] = $properties;
            foreach ($groupedArray[$group] as $existingSelector => $existingProperties) {
                if ($selector === $existingSelector) {
                    continue;
                }
                if (str_contains($existingSelector, $selector) || str_contains($selector, $existingSelector)) {
                    $groupedArray[$group][$existingSelector] = array_merge($groupedArray[$group][$existingSelector], $properties);
                    unset($groupedArray[$group][$selector]);
                    break;
                }
            }
        }
        return $groupedArray;
    }

    private function getGroup($selector) {
        $types = [
            ['selectors' => [['h1'], ['h2'], ['h3'], ['h4'], ['h5'], ['h6'], ['a', 'h1'], ['a', 'h2']], 'category' => 'Fonts'],
            ['selectors' => [['card'], ['card-header'], ['card-body'], ['card-footer']], 'category' => 'Cards'],
            ['selectors' => [['btn'], ['.btn'], ['btn-success'], ['btn-primary'], ['btn-warning'], ['btn-danger'], ['btn-info'], ['btn-default']], 'category' => 'Buttons'],
            ['selectors' => [['pos-'], ['.pos-']], 'category' => 'Positions'],
            ['selectors' => [['nav-'], ['.nav-'], ['navbar'], ['dropdown'], ['menu']], 'category' => 'Menu'],
            ['selectors' => [['jumbotron-'], ['.jumbotron-'], ['.jumbotron'], ['jumbotron ']], 'category' => 'Jumbotron'],
            ['selectors' => [['list-group-'], ['.list-group-']], 'category' => 'ListGroup'],
            ['selectors' => [['.form-control'], ['form-control'], ['.valid'], ['.error']], 'category' => 'Forms'],
            ['selectors' => [['body']], 'category' => 'Body'],
            ['selectors' => [['img-'], ['.img-'], ['img']], 'category' => 'Images']
        ];

        foreach ($types as $type) {
            $matches = 0;
            foreach ($type['selectors'] as $selectorGroup) {
                $selectorMatch = true;
                foreach ($selectorGroup as $selectorPart) {
                    if ($selectorPart !== '' && !str_contains($selector, $selectorPart)) {
                        $selectorMatch = false;
                        break;
                    }
                }
                if ($selectorMatch) {
                    $matches++;
                } else {
                    // if any selector in the group does not match, skip the rest of the groups
                    break;
                }
            }
            if ($matches === count($type['selectors'])) {
                return $type['category'];
            }
        }
        return 'Other';
    }

    public function getArrayData($array, $key, $subkey = null): array
    {
        $result = array();
        foreach ($array as $k => $v) {
            if ($k == $key) {
                if ($subkey) {
                    if (isset($v[$subkey])) {
                        $result[] = $v[$subkey];
                    }
                } else {
                    $result[] = $v;
                }
            }
            if (is_array($v)) {
                $result = array_merge($result, $this->getArrayData($v, $key, $subkey));
            }
        }
        return $result;
    }

    public function generateFields($properties, $key, $subkey)
    {
        // build form fields for each css property
        echo '<form id="css-update-form" method="POST">';
//        if (!empty($this->filename)) {
//            echo '<p>processed: ' . $this->filename . '</p>';
//        } else {
//            return; // exit function
//        }
        if (!empty($key)) {
            return $this->getArrayData($properties, $key, $subkey);
        }
        else {
            return;
        }
    }


    public function generateCssUpdateForm($properties): void
    {
        echo "<h1>Update CSS</h1>";
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

}