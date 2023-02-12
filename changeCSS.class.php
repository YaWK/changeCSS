<?php
class CssParser {
    public function read($cssFile, $returnSelectorOnly = false, $regExpFilter = ""): array
    {
        $result = [];
        $error = [];

        try {
            $css = file_get_contents($cssFile);
        } catch (Exception $e) {
            $error[] = 'Exception '.$e.' File not found or cannot be read: ' . $cssFile;
            $result['debug-errors-cssreader'] = $error;
            return $result;
        }

        // Remove comments from CSS
        $css = preg_replace('/\/\*[\s\S]*?\*\//', '', $css);

        // Extract selectors and rules
        $selectorRegExp = '/([^{]+)\{([^}]*)}/';
        if (preg_match_all($selectorRegExp, $css, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $selector = trim($match[1]);
                if ($returnSelectorOnly) {
                    if (!$regExpFilter || preg_match($regExpFilter, $selector)) {
                        $result[] = $selector;
                    }
                } else {
                    list($rules, $rules_arr, $rule, $parts, $selectors, $sel, $result) = $this->extracted($match[2], $selector, $result);
                }
            }
        }
        // Extract media queries and nested selectors/rules
        $mediaRegExp = '/@media[^{]+\{([\s\S]*?)}/';
        if (preg_match_all($mediaRegExp, $css, $mediaMatches)) {
            foreach ($mediaMatches[0] as $i => $mediaMatch) {
                $mediaRules = [];
                if (preg_match_all($selectorRegExp, $mediaMatches[1][$i], $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $selector = trim($match[1]);
                        list($rules, $rules_arr, $rule, $parts, $selectors, $sel, $result) = $this->extracted($match[2], $selector, $mediaRules);
                    }
                }
                $mediaSelector = $mediaMatches[0][$i];
                $result[$mediaSelector] = $mediaRules;
            }
        }

        else {
            $error[] = 'No selectors found in CSS';
        }

        if ($error) {
            $result['debug-errors-cssreader'] = $error;
        }

        return $result;
    }

    public function showData($result, $selectorFilter): void
    {
        if (is_array($result))
        {
            foreach ($result as $selector => $data)
            {
                // check if selector was set
                if (!empty($selectorFilter) && (is_string($selectorFilter)))
                {
                    // filter css selectors for processing
                    if(str_contains($selector, $selectorFilter))
                    {
                        // draw selector name as title
                        echo "<h2>".$selector."</h2>";

                        // start with allowed css selectors
                        if (is_array($data))
                        {
                            // process content data of selector
                            foreach ($data as $property => $value)
                            {   // process properties (detect and set appropriate fields)

                                // detect type:
                                if (strpos($property, "color") || ($property == "color"))
                                {   // draw color picker field
                                    echo '&nbsp;&nbsp;<label for="$property"><b class="text-muted">'.$property.' : '.$value.'</b> <input type="text" id="'.$property.'" name="'.$property.'" data-jscolor="{previewSize:182, borderRadius:6, padding:0, sliderSize:110, 
    shadowColor:\'rgba(0,0,0,0.15)\'}" class="form-control" value="'.$value.'" style="width: 300px;"></label><br>';
                                }
                                else if (strpos($property, "-shadow") || ($property == "box-shadow"))
                                {   // draw color picker field
                                    echo '&nbsp;&nbsp;<label for="$property"><b class="text-muted">'.$property.' : '.$value.'</b> <input type="text" id="'.$property.'" name="'.$property.'" class="form-control" value="'.$value.'" style="width: 234px;></label><br>';
                                }
                                else
                                {   // simple variant: display css property and value
                                    echo '&nbsp;&nbsp;<b class="text-danger">'.$property.' : </b> = '.$value.'<br>';
                                    //echo '&nbsp;&nbsp;<b class="text-muted">'.$property.' : </b> = '.$value.'<br>';
                                }

                            }
                        }
                        else
                        {   // not an array: this should not be possible.
                            echo $data."<br>";
                        }
                        echo "<hr>";
                    }
                }

            }
        }
        else {
            die('$result is not an array!');
        }
    }

    /**
     * @param $match
     * @param string $selector
     * @param array $result
     * @return array
     */
    public function extracted($match, string $selector, array $result): array
    {
        $rules = array_filter(array_map('trim', explode(';', $match)));
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
        return array($rules, $rules_arr, $rule, $parts, $selectors, $sel, $result);
    }
}

echo '<div class="container"><div class="col-md-12">';
$parser = new CssParser();
$result = $parser->read('bootstrap.css');
$parser->showData($result, ".btn");


// print_r($result);
echo "</div></div>";
?>
<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jscolor.min.js"></script>
