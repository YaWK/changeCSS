<script src="assets/js/jscolor.min.js"></script>
<?php
// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if file was uploaded
if(isset($_FILES['css-file']) && $_FILES['css-file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['css-file']['tmp_name'];
    $filename = $_FILES['css-file']['name'];
    $css = file_get_contents($file);
    // Remove comments from CSS
    $css = preg_replace('/\/\*[\s\S]*?\*\//', '', $css);

    // Extract selectors and rules
    $selectorRegExp = '/([^{]+)\{([^}]*)\}/';
    $returnSelectorOnly = false;
    $regExpFilter = '';

    $result = [];
    $error = [];

    if (preg_match_all($selectorRegExp, $css, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $selector = trim($match[1]);
            if ($returnSelectorOnly) {
                if (!$regExpFilter || preg_match($regExpFilter, $selector)) {
                    $result[] = $selector;
                }
            } else {
                $rules = array_filter(array_map('trim', explode(';', $match[2])));
                $rules_arr = [];
                foreach ($rules as $rule) {
                    $parts = array_map('trim', explode(':', $rule));
                    if (count($parts) == 2) {
                        $rules_arr[$parts[0]] = $parts[1];
                    }
                }
                $selectors = array_map('trim', explode(',', $selector));
                foreach ($selectors as $sel) {
                    $result[$sel] = $rules_arr;
                }
            }
        }
    }
    // Extract media queries and nested selectors/rules
    $mediaRegExp = '/@media[^{]+\{([\s\S]*?)\}/';
    if (preg_match_all($mediaRegExp, $css, $mediaMatches)) {
        foreach ($mediaMatches[0] as $i => $mediaMatch) {
            $mediaRules = [];
            if (preg_match_all($selectorRegExp, $mediaMatches[1][$i], $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $selector = trim($match[1]);
                    $rules = array_filter(array_map('trim', explode(';', $match[2])));
                    $rules_arr = [];
                    foreach ($rules as $rule) {
                        $parts = array_map('trim', explode(':', $rule));
                        if (count($parts) == 2) {
                            $rules_arr[$parts[0]] = $parts[1];
                        }
                    }
                    $selectors = array_map('trim', explode(',', $selector));
                    foreach ($selectors as $sel) {
                        $mediaRules[$sel] = $rules_arr;
                    }
                }
            }
            $mediaSelector = $mediaMatches[0][$i];
            $result[$mediaSelector] = $mediaRules;
        }
    }

    echo '<form id="css-update-form" method="POST">';
    foreach ($result as $property => $value){
        if (is_array($value)){
            echo '<h2>'.$property.'</h2><hr>';
            foreach ($value as $data => $datavalue){
                echo '<label for="$property"><b class="text-muted">'.$data.'</b> <input type="text" id="'.$data.'" name="'.$data.'" data-jscolor="{previewSize:182, borderRadius:6, padding:0, sliderSize:110, 
    shadowColor:\'rgba(0,0,0,0.15)\'}" class="form-control color" value="'.$datavalue.'" style="width: 300px;"></label><br>';
            }
        echo '<br><br>';
        }
    }
    echo '<input type="hidden" name="filename" value="'.$filename.'">
        <button type="submit">Save</button>
        </form>';

} else {
    echo 'File upload failed';
}