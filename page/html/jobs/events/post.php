<?php
/**
* Project Tank Webpage
* save posted news page
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
_lib("DB");
_lib("DBHandler");
_lib("Html");
_lib("EventEditor");
/* ===================================================================================== */
$isLogin = $_page["login"] !== false && isset($_page["user"]["player"]);
if(!$isLogin) _error(ERROR_MISSING_LOGIN);
/* ===================================================================================== */
/** @var array $_user */
$_user = $_page["user"];
/** @var WotPlayer $_player */
$_player = $_user["player"];
/* ===================================================================================== */
$options = ["login"=>$isLogin, "clan"=>$_player->hasClan(), "settings"=>$_user["settings"]];
$_route = isset($_GET["b"]) ? $_GET["b"] : null;
$_route = Router::getRoute($_route, $options);
//$_route = isset($_GET["b"]) ? Router::getRoute2($_GET["b"], $options) : Router::getDefault($isLogin);
/* ===================================================================================== */
$isEvent = isset($_POST["event"]) && is_array($_POST["event"]);
if(!$isEvent) finish($_route);
/* ===================================================================================== */
$event = $isEvent ? $_POST["event"] : null;
if(!isset($event["title"]) || empty($event["title"])) _warning(WARNING_POST_MISSING_TITLE, $_route);
if(!isset($event["text"]) || empty($event["text"])) _warning(WARNING_POST_MISSING_TEXT, $_route);
if(!isset($event["start"]) || empty($event["start"])) _warning(WARNING_POST_MISSING_TIME_START, $_route);
if(!isset($event["end"]) || empty($event["end"])) _warning(WARNING_POST_MISSING_TIME_END, $_route);
if(!isset($event["type"]) || empty($event["type"])) _warning(WARNING_POST_MISSING_EVENT_TYPE, $_route);
$isEdit = isset($event["id"]);
/* ===================================================================================== */
$action = $isEdit ? "edit" : "create";
$langActionText = Html::getEventActionLang($action);
/* ===================================================================================== */
?>
<!DOCTYPE html>
<html>
<head>
	<title><?=PAGE_BRAND;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.2.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/base.css"> 
	<style>
		html, body{
			height: 100%;
		}
		img{
			margin: 10px 0;
		}
		.b-row{
			position: relative;
			width: 100%;
			height: 100%;
		}
		.box {
			position: absolute;
			top: 50%;
			left: 50%;
			height: 50px;
			width: 600px;
			margin: -25px 0 0 -300px;
			text-align: center;
			border: 0;
		}
	</style>
</head>
<body>
	<div class='b-row'>
		<div class='box'>
			<img src='images/loader/loader-bar.gif' alt='loader'/>
			<p>Bitte warten Sie einen Moment.</p>
			<small><?=$langActionText?></small>
		</div>
	</div>
<?php
flush();
/* prepare data ======================================================================== */
$options = ["useHyphens"=>true];
$uid = Html::clean($event["title"], $options)."-".date("YmdHis", time())."-".uniqid();
if(!isset($event["summary"])) $event["summary"] = Html::truncate($event["text"], Html::EVENT_LG_SUMMARY_MAX_LEN, ["removeLineBreak"=>true,"ellipsis"=>"...","removeHtml"=>true]);
/* ===================================================================================== */
if($debug){
    Debug::s(URL_ROOT.$_route);
    Debug::i($uid);
    Debug::r($event);
//    exit();
}
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
//$dbh->debug($debug);
/* write to db ========================================================================= */
if($dbh->isConnection()){
    // get event type default data
    $eventType = $dbh->parse($dbh->getEventTypeByID($event["type"]), false);
    if($eventType === false) finish(_error(ERROR_DB_GET_EVENT_TYPE, null, $debug, true));

    $eventTypeOptions = $dbh->parse($dbh->getEventTypesOptions(["fields"=>"name,inputType"]), false);
    if($eventType === false) finish(_error(ERROR_DB_GET_EVENT_TYPE, null, $debug, true));

    //todo:: go on
    $sqlMaps = isset($event["maps"])
        ? array_map(function($a){
                return [
                    "mapID"=>$a["id"],
                    "modeID"=>$a["mode"],
                ];
            }, $event["maps"])
        : null;

    $sqlPrices = isset($event["prices"])
        ? array_map(function($a){
                return [
                    "rank_from"=>$a["from"],
                    "rank_to"=>!empty($a["to"]) ? $a["to"] : null,
                    "gold"=>!empty($a["gold"]) ? $a["gold"] : null,
                    "others"=>!empty($a["others"]) ? $a["others"] : null,
                ];
            }, $event["prices"])
        : null;

    $sqlOptions = [];
    foreach($eventTypeOptions as $name=>$eOption) {
        $inputType = $eOption["inputType"];
        $default = $eventType[$name];
        $eventValue = isset($event["options"][$name]) ? $event["options"][$name] : null;
        $sqlOptions[$name] = EventEditor::getEventOptionValue($name, $inputType, $eventValue, $default);
    }

    // briefingID
    $briefingStart = isset($sqlOptions["briefing"]) ? strtotime($sqlOptions["briefing"]) : null;
    $isBriefing = $briefingStart !== null;
    $briefingID = null;
    if($isBriefing)
        $briefingID = $isEdit && isset($event["briefingID"]) && !empty($event["briefingID"])
            ? $event["briefingID"]
            : EventEditor::generateBriefingID($event["start"],$event["end"],$event["title"],$_player->getID());

    // check for duplicate briefingID
    if($isBriefing && !$isEdit){
        $idExists = true;
        $count = 0;
        while($idExists){
            $result = $dbh->existsBriefingID($briefingID);
            if($result === false) finish(_error(ERROR_DB_GET_BRIEFINGID_EXISTS, null, $debug, true));
            else $idExists = $result === 1;
            if($idExists) $briefingID = EventEditor::generateBriefingID($event["start"],$event["end"],$event["title"],$_player->getID());
            if($debug && $idExists) Debug::e("duplicate briefing id: ".$briefingID);
            $count++;
            if($count > EventEditor::BRIEFING_ID_MAX_GEN)
                finish(_error(ERROR_DB_LIMIT_BRIEFINGID_GEN, null, $debug, true));
        }
    }

    $sqlData = [
        "eventID"=>$isEdit ? $event["id"] : null,
        "userID"=>$_player->getID(),
        "typeID"=>$event["type"],
        "title"=>$event["title"],
        "text"=>$event["text"],
        "summary"=>$event["summary"],
        "start"=>strtotime($event["start"]),
        "end"=>strtotime($event["end"]),
        "maxUsers"=>isset($event["maxUsers"]) ? $event["maxUsers"]: null,
        "clanID"=>isset($sqlOptions["clan"]) && $sqlOptions["clan"] == 1 && $_player->hasClan() ? $_player->getClanID() : null,
//        "hidden"=>isset($event["hidden"]) ? $event["hidden"]: $eventType["hidden"],
//        "public"=>isset($event["public"]) ? $event["public"]: null,
//        "password"=>isset($event["password"]) ? $event["password"]: null,
        "briefing"=>$isBriefing ? $briefingStart : null,
        "briefingID"=>$isBriefing ? $briefingID : null,
        "uid"=>$uid,
        "maps"=>$sqlMaps,
        "prices"=>$sqlPrices,
//        "options"=>$sqlOptions,
    ];

    if($debug){
        Debug::e($eventType);
        Debug::e($eventTypeOptions);
        Debug::s($briefingStart);
        Debug::s($sqlOptions);
        Debug::r($sqlData);
    }

    $sqlData = array_merge($sqlOptions, $sqlData);
    $dbh->debug($debug);
	$result = $isEdit
        ? $dbh->updateEvent($sqlData)
        : $dbh->postEvent($sqlData);
	if($dbh->isDebug()) Debug::v($result);
    if($debug) exit();
    if(!$result){
        finish(_error(ERROR_DB_SET_EVENT, null, $debug, true));
    }
	else if($debug) Debug::h($result);
} else
    finish(_error(ERROR_DB_CONNECTION, null, $debug, true));
/* ===================================================================================== */	
if($debug) exit();
finish(URL_ROOT.$_route);
/* ===================================================================================== */
/* ===================================================================================== */
function finish($route){
    echo "<script>window.location.href = '$route'</script>
        </body>
    </html>";
    exit();
}
?>