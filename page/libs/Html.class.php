<?php
/**
* Project Tank Webpage
* handler for creating custom html elements
* @author Steffen Lange
*/
class Html{

	/* ===================================================================================== */

    public static function css($urls){
        $tmp = "<link rel='stylesheet' type='text/css' href='{{url}}'/>\n";
        return !empty($urls) ? self::templateList($tmp, 'url', $urls) : null;
    }

    public static function js($urls){
        $tmp = "<script src='{{url}}'></script>\n";
        return !empty($urls) ? self::templateList($tmp, 'url', $urls) : null;
    }

	/* ===================================================================================== */

	public static function toDataString($data){
		if(!is_array($data)) return null;
		$out = "";
		foreach($data as $name=>$value){
			$out .= " data-".$name."='".$value."'";
		}
		return $out;
	}
	
	public static function createFaImg($options=[]){
		if(is_string($options)) return "<i class='fa fa-".$options."'></i>";
		$type = isset($options["type"]) ? " fa-".$options["type"] : null;
		$class = isset($options["class"]) ? " ".$options["class"] : null;
		$content = isset($options["content"]) ? $options["content"] : null;
		return "<i class='fa".$type.$class."'>".$content."</i>";
	}

    public static function template($tmp, $data, $options=[]){
        $out = $tmp;
        $empty = isset($options["empty"]) && $options["empty"];
        $path = isset($options["path"]) ? trim($options["path"], ".") : null;
        foreach($data as $key=>$value){
            if(is_string($value)) $out = preg_replace('({{'.$key.'}})',$value,$out);
            else if(is_array($value)) $out = self::template($out, $value, ["path"=>$path.".".$key]);
        }
        if($empty) $out = self::template($out, ['[^}}]+'=>'']);
        return $out;
    }

    public static function templateList($tmp, $key, $list){
        if(is_string($list)) return self::template($tmp, [$key=>$list]);
        else{
            $out = "";
            foreach($list as $item){
                $out .= self::template($tmp, [$key=>$item]);
            }
            return $out;
        }
    }

    /* ===================================================================================== */

	public static function createSwitchButton($id, $options, $inRow=false){
		$title = isset($options["title"]) ? $options["title"] : null;
		$descr = isset($options["descr"]) ? $options["descr"] : null;
		$class = isset($options["class"]) ? $options["class"] : null;
		$inputName = isset($options["input"]) ? " name='".$options["input"]."'" : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		$activeValue = isset($options["active"]) ? $options["active"] : null;
		
		$html = "<div class='btn-switch' id='".$id."'>";
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
	
	public static function createBoardInfo($options){
		$id = isset($options["id"]) ? " id='".$options["id"]."'" : null;
		$title = isset($options["title"]) ? $options["title"] : null;
		$type = isset($options["type"]) ? "-".$options["type"] : null;
		$class = isset($options["class"]) ? " ".$options["class"] : null;
		$hasElements = isset($options["elements"]) && !empty($options["elements"]);
		
		$rows = "";
		if($hasElements) foreach($options["elements"] as $e){
			$eTitle = isset($e["title"]) ? $e["title"] : null;
			$eContent = isset($e["content"]) ? $e["content"] : null;
			$disabled = isset($e["disabled"]) && $e["disabled"] ? " disabled": null;
			$faimg = isset($e["faimg"]) ? self::createFaImg($e["faimg"]) : null;
			$rows .= "<li class='row".$disabled."'>".$faimg.$eTitle."<span class='pull-right'>".$eContent."</span></li>";
		}
		
		return "<div".$id." class='board-info".$type.$class."'>
			<h6>".$title."</h6>
			<ul>".$rows."</ul>
		</div>";
	}
}