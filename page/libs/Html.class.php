<?php
/**
* Project Tank Webpage
* handler for creating custom html elements
* @author Steffen Lange
*/
class Html{

	/* ===================================================================================== */
	public static function toDataString($data){
		if(!is_array($data)) return null;
		$out = "";
		foreach($data as $name=>$value){
			$out .= " data-".$name."='".$value."'";
		}
		return $out;
	}
	
	
	/* ===================================================================================== */

	public static function createSwitch($id, $options, $inRow=false){
		$title = isset($options["title"]) ? $options["title"] : null;
		$descr = isset($options["descr"]) ? $options["descr"] : null;
		$class = isset($options["class"]) ? $options["class"] : null;
		$inputName = isset($options["input"]) ? " name='".$options["input"]."'" : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		$activeValue = isset($options["active"]) ? $options["active"] : null;
		
		$html = "<div class='switch' id='".$id."'>";
		if($hasElements){
			$count = 0;
			foreach($options["elements"] as $txt=>$value){
				$active = $activeValue != $value ? "" : " checked";
				$html .= "<input id='".$id."_switch_".$count."' type='radio' value='".$value."'".$inputName.$active."/><label for='".$id."_switch_".$count."'>".$txt."</label>";
				$count++;
			}
		}
		$html .= "</div>";	
		if($inRow) return self::createSettingsRow(null, $class, $title, $descr, $html);
		else return $html;
	}
	
	public static function createDataSelect($id, $options, $inRow=false){
		$title = isset($options["title"]) ? $options["title"] : null;
		$descr = isset($options["descr"]) ? $options["descr"] : null;
		$class = isset($options["class"]) ? $options["class"] : null;
		$inputName = isset($options["input"]) ? " name='".$options["input"]."'" : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		$activeValue = isset($options["active"]) ? $options["active"] : null;
		
		$html = "<select id='".$id."' class='data-select vertical-align'".$inputName.">";
		if($hasElements)
			foreach($options["elements"] as $option){
				$txt = isset($option["text"]) ? $option["text"] : null;
				$value = isset($option["value"]) ? " value='".$option["value"]."'" : null;
				$data = isset($option["data"]) ? self::toDataString($option["data"]) : null;
				$active = isset($option["value"]) && $activeValue != $option["value"] ? "" : " selected";
				$html .= "<option".$value.$data.$active.">".$txt."</option>";
			}
		$html .= "</select>";
		
		if($inRow) return self::createSettingsRow(null, $class, $title, $descr, $html);
		else return $html;
	}
	
	public static function createSettingsRow($id=null, $class=null, $title=null, $descr=null, $content=null){
		$htmlID = isset($id) ? " id='$id'" : null;
		$htmlClass = isset($class) ? " $class" : null;
		return "<ul".$htmlID." class='row row-table".$htmlClass."'>
			<li class='col-md-6'>
				<h5>$title</h5>
				<small>$descr</small>
			</li>
			<li class='col-md-6'>$content</li>
		</ul>";
	}
}