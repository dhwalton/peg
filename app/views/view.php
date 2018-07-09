<?php

/**
 * Shamelessly stolen from StackOverflow, modified a bit
 * https://stackoverflow.com/questions/14143865/render-a-view-in-php
 * Credit goes to Martin Samson
 * View-specific wrapper.
 * Limits the accessible scope available to templates.
 */
class View {
    /**
     * Template being rendered.
     */
    protected $template = null;
    private $layoutPath;
    private $layout;
	private $data;
	
    /**
     * Initialize a new view context.
     */
    public function __construct($template, $useLayout) {
        $this->template = $template;

        // this assigns the layout file
		if ($useLayout) {
			$this->layoutPath = ROOT_PATH . VIEW_PATH . "_layout.php";
			ob_start();
			include($this->layoutPath);
			$content = ob_get_contents();
			ob_end_clean();
			$this->layout = $content;
		}
    }

    /**
     * Safely escape/encode the provided data.
     */
    public function h($data) {
        return htmlspecialchars((string) $data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render the template, returning it's content.
     * @param array $data Data made available to the view.
     * @return string The rendered template.
     */
    public function render(Array $data) {
		$this->data = $data;
        extract($data);

        ob_start();
        include(ROOT_PATH . VIEW_PATH . $this->template);
        $content = ob_get_contents();
        ob_end_clean();
        
		// if no layout file is found, return the content
		if ($this->layout == "") {
			return($content);
		}
		
        // replace RENDERBODY in the layout file with the content
        return str_replace("RENDERBODY", $content, $this->layout);
    }
	
	
	public function DropDown($arr, $name, $valueKey, $nameKey, $selectedValue) {
		//print_r($arr);
		if (count($arr) > 0) {
			$result = "\n" . '<select name="' . $name . '" id="' . $name . '">';
			for ($i = 0; $i < count($arr); $i++) {
				$result .= "\n\t" . '<option value="'. $arr[$i][$valueKey] . '"';
				if ($arr[$i][$valueKey] == $selectedValue) $result .= " selected";
				$result .= ">" . $arr[$i][$nameKey] . "</option>";
			}
			$result .= "\n</select>";	
		} else {
			$result = "Empty Array!";
		}
		
		
		echo $result;
	}
	
	public function TextField($name, $val) {
		echo '<input type="text" name="' . $name . '" id="' . $name . '" value="' . $val . '" />';
	}
}

?>