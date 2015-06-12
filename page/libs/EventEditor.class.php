<?php
//_lib("Editor");
//_lib("Html");
_lib("WotEvent");
class EventEditor{

    const DE_HEADLINE_EDIT = "Event bearbeiten";
    const DE_HEADLINE_NEW = "Event erstellen";
    const DE_BT_SAVE_EDIT = "Speichern";
    const DE_BT_SAVE_NEW = "Erstellen";
    const DE_TITLE_NEW = "Welche Art von Event soll erstellt werden?";
    const DE_ERROR_TYPE = "Fehler beim Erstellen (1)";
    const DE_ACTIVATE = "Aktivieren";
    const DE_EMPTY_SECTION = "Keine Einstellungen m&ouml;glich.";
    const DE_OPTIONAL_SECTION = "(optional)";
    const DE_GOLD_CURRENCY = " Gold";
    const DE_PRICE_RANK_SEP = " - ";

    const BRIEFING_ID_MAX_GEN = 32;

    private static $eventTypes = [];
    private static $typeOptions = [];
    /** @var WotMapObject[] array  */
    private static $mapList = [];
    private static $gameModes = [];

    public static function setEventTypes($data){self::$eventTypes = $data;}
    public static function setTypeOptions($data){self::$typeOptions = $data;}
    public static function setMapList($data){self::$mapList = $data;}
    public static function setGameModes($data){self::$gameModes = $data;}


    /**
     * @param array $arr
     * @param string $value
     * @param string $key
     * @return mixed
     */
    private static function getArrayMatch($arr, $value, $key="typeID"){
        if(is_callable($key)){
            foreach($arr as $t) {
                if ($key($t, $value) !== null) return $t;
            }
        }else{
            foreach($arr as $t)
                if($t[$key] == $value) return $t;
        }
        return null;
    }

    /**
     * @param WotEvent $event
     * @param array $eventTypes
     */
    public static function generate($event){
        if($event->getTypeID() !== null){
            self::renderEditor($event, self::getArrayMatch(self::$eventTypes, $event->getTypeID()));

        }
        else self::renderSetType();
    }

    /**
     * @param string $start
     * @param string $end
     * @param string $tiitle
     * @param int $userID
     * @return string(40)
     */
    public static function generateBriefingID($start,$end,$tiitle,$userID){
        return sha1(time().$start.$end.$tiitle.$userID);
    }

    /**
     * @param string $name
     * @param string $inputType
     * @param mixed $eventValue
     * @param int $default
     * @return mixed
     */
    public static function getEventOptionValue($name, $inputType, $eventValue, $default){
        switch($inputType){
            case "checkbox":
                return empty($eventValue) ? $default : $eventValue;
            default:
                return empty($eventValue) ? null : $eventValue;
        }
    }

    /**
     * @param WotEvent $event
     * @param mixed $type
     * @internal param array $eventTypes
     * @internal param array $typeOptions
     */
    public static function renderEditor($event, $type){
        if($type === null || empty($type)) {
            echo self::DE_ERROR_TYPE;
            return;
        }

        $isEdit = $event->getID() !== null;
        $i18nHeadline = $isEdit ? self::DE_HEADLINE_EDIT : self::DE_HEADLINE_NEW;
        $i18nSaveNewsButton = $isEdit ? self::DE_BT_SAVE_EDIT : self::DE_BT_SAVE_NEW;
        $bsColor = isset($type["bsColor"]) ? $type["bsColor"] : "danger";
        $typeName = $type["name_i18n"];
        $eventMaps = $event->getMaps();
        $eventPrices = $event->getPrices();
        ?>
        <form action="<?=URL_ROOT.ROUTE_EVENT_POST;?>" method="post">
            <?=($isEdit?"<input type='hidden' name='event[id]' value='".$event->getID()."'/>":null)?>
            <input type="hidden" name="event[type]" value="<?=$event->getTypeID()?>"/>
            <div class="bs-callout bs-callout-<?=$bsColor?> bs-callout-custom bc-dash">
                <h4><?=$i18nHeadline.": ".$typeName?></h4>
                <div class="callout-content row">
                    <div class="col-md-12">
                        <label for="inTitle">Titel</label>
                        <input type="text" id="inTitle" class="form-control" name="event[title]" maxlength="40" placeholder="Titel.." value="<?=$event->getTitle()?>"/>
                    </div>
                </div>
                <div class="callout-content row">
                    <div class="col-md-6">
                        <label for="inTimeStart">Startzeit</label>
                        <div id="dtTimeStart" class="input-group datetime-picker" data-min="#dtTimeEnd" data-max="#option_briefing .datetime-picker">
                            <input id="inTimeStart" name="event[start]" type="text" class="form-control" value="<?=$event->getStart()?>"/>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-fw fa-calendar"></i>Ausw&auml;hlen</button>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="inTimeEnd">Endzeit</label>
                        <div id="dtTimeEnd" class="input-group datetime-picker" data-max="#dtTimeStart">
                            <input id="inTimeEnd" name="event[end]" type="text" class="form-control" value="<?=$event->getEnd()?>"/>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><i class="fa fa-fw fa-calendar"></i>Ausw&auml;hlen</button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="callout-content row">
                    <div class="col-md-12">
                        <label for="inText">Beschreibung/Regelwerk</label>
                        <textarea id="inText" name="event[text]" class="ckeditor form-control"><?=$event->getText()?></textarea>
                    </div>
                    <div class="col-md-12">
                        <br>
                        <label for="inSummary">Zusammenfassung (optional)</label>
                        <textarea id="inSummary" name="event[summary]" class="form-control" maxlength="250"><?=$event->getSummary()?></textarea>
                    </div>
                </div>
            </div>
            <div class="bs-callout bs-callout-primary bs-callout-custom bc-dash event-prices">
                <h4>Preise</h4>
                <?php
                $index = 0;
                foreach($eventPrices as $prices){
                    $id = $prices->getPriceID();
                    $from = $prices->getRankFrom();
                    $to = $prices->getRankTo();
                    $gold = $prices->getRankFrom();
                    $others = $prices->getOthers();
                    $isGold = !empty($gold);
                    $tmpData = [
                        "priceID"=>$id,
                        "from"=>$from,
                        "to"=>$to,
                        "gold"=>$gold,
                        "others"=>$others,
                        "index"=>$index,
                    ];
                    $index++;
                    echo Html::template(self::TMP_EVENT_PRICE, $tmpData, ["empty"=>true]);
                }
                ?>
                <div id="eventPriceAdd" class="callout-content row">
                    <div class="col-md-4">
                        <label>Platzierung</label>
                        <div class="input-group">
                            <span class="input-group-addon">Von</span>
                            <input id="inPriceFrom" data-field="from" data-required="true" class="form-control" type="number" min="1" max="100"/>
                            <span class="input-group-addon">Bis</span>
                            <input id="inPriceTo" data-field="to" class="form-control" type="number" max="100"/>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label>Gewinn</label>
                        <div class="input-group">
                            <span class="input-group-addon">Gold</span>
                            <input id="inPriceGold" data-field="gold" class="form-control" type="number" min="0"/>
                            <span class="input-group-addon">Andere</span>
                            <input id="inPriceOthers" data-field="others" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="bt-add-item btn btn-warning btn-end pull-right" data-template="#tmpEventPrice" data-method="before" data-target="#eventPriceAdd" data-input="#inPriceFrom,#inPriceTo,#inPriceGold,#inPriceOthers" data-index=".price-row"><i class="fa fa-plus"></i> Hinzuf&uuml;gen</button>
                    </div>
                </div>
            </div>
            <div class="bs-callout bs-callout-success bs-callout-custom bc-dash event-maps">
                <h4>Karten und Gefechtart</h4>
                <?php
                $index = 0;
                foreach($eventMaps as $map){
                    $id = $map->getMapID();
                    $wotMap = self::getArrayMatch(self::$mapList, $id, function($ele,$key){return $ele->getMapID() == $key ? $ele : null;});
                    $name = $wotMap !== null ? $wotMap->getName() : "unknown";
                    $name_i18n = $wotMap !== null ? $wotMap->getNameI18n() : "Unbekannte Karte";
                    $mode = self::getArrayMatch(self::$gameModes, $map->getModeID(), "modeID");
                    $modeID = $mode !== null ? $mode["modeID"] : null;
                    $modeName = $mode !== null ? $mode["name_i18n"] : "Unbekannter Modus";
                    $src = PATH_IMG_MAPS_THUMBS.$name.'.'.EXT_IMG_MAP;

                    $tmpData = [
                        "index"=>$index,
                        "mapID"=>$id,
                        "modeID"=>$modeID,
                        "image"=>$src,
                        "modeName"=>$modeName,
                        "mapName"=>$name_i18n,
                    ];
                    $index++;
                    echo Html::template(self::TMP_EVENT_MAP, $tmpData);
                }
                ?>
                <div id="eventMapAdd" class="callout-content row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <?php
                        // render gamemode select
                        $elements = array_map(function($a){
                            return [
                                "text"=>htmlentities($a["name_i18n"]),
                                "value"=>$a["modeID"]
                            ];
                        }, self::$gameModes);
                        $options = [
                            "elements"=>$elements,
                        ];
                        echo Html::createDataSelect("inGameMode", $options);

                        // render wot maps select
                        $elements = array_map(function($a){
                            return [
                                "text"=>htmlentities($a->getNameI18n(), ENT_QUOTES,'ISO-8859-1'),
                                "value"=>$a->getMapID(),
                                "data"=>[
                                    "imagesrc"=>PATH_IMG_MAPS_THUMBS.$a->getName().'.'.EXT_IMG_MAP,
                                    "description"=>htmlentities($a->getDescriptionI18n(), ENT_QUOTES,'ISO-8859-1'),
                                ]
                            ];
                        }, self::$mapList);
                        $options = [
                            "elements"=>$elements,
                        ];
                        echo Html::createDataSelect("inMaps", $options);

                        ?>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-12">
                        <button type="button" class="bt-add-map btn btn-warning btn-end pull-right" data-template="#tmpEventMap" data-before="#eventMapAdd"><i class="fa fa-plus"></i> Hinzuf&uuml;gen</button>
                    </div>
                </div>
            </div>
            <div class="bs-callout bs-callout-primary bs-callout-custom bc-dash">
                <h4>Allgemeine Einstellungen</h4>
                <?php
                $count = 0;
                foreach(self::$typeOptions as $name=>$op){
                    if(!isset($type[$name])) continue;

                    $value = $type[$name];
                    $isCheckbox = $op["inputType"] == "checkbox";
                    $isDisabled = ($value != "2" && $isCheckbox) || $value == "0";

                    // skip rendering if option is disabled by event type
                    if($isDisabled) continue;

                    $evIsActive = $event->isActiveTypeOptionValue($name);
                    $optional = $value == "2" ? self::DE_OPTIONAL_SECTION : null;
                    $input = [
                        "type"=>$op["inputType"],
                        "name"=>"event[options][$name]",
                        "checked"=>$isCheckbox && ($isEdit ? $evIsActive : $value == "1"),
                        "value"=>$isCheckbox ? "1" : null,
                        "disabled"=>($value != "2" && $isCheckbox) || $value == "0",
                    ];
                    $row = [
                        "id"=>"option_".$name,
                        "title"=>$op["title_i18n"],
                        "descr"=>$optional.' '.$op["description_i18n"],
                        "content"=>Html::createInput($input),
                    ];

                    echo '<div class="callout-content row">';
                    echo Html::createSettingsRowParam($row);
                    echo '</div>';

                    $count++;
                }
                if($count === 0) echo '<p>'.self::DE_EMPTY_SECTION.'</p>';
                ?>
            </div>
            <div class="row button-bar">
                <a href="<?=URL_ROOT.ROUTE_EVENTS?>" class="btn btn-danger pull-left"><i class="fa fa-fw fa-times"></i>Abbrechen</a>
                <button type="submit" class="btn btn-success pull-right"><i class="fa fa-fw fa-plus"></i><?=$i18nSaveNewsButton?></button>
            </div>
        </form>
        <script type="text/template" id="tmpEventMap"><?=self::TMP_EVENT_MAP?></script>
        <script type="text/template" id="tmpEventPrice"><?=self::TMP_EVENT_PRICE?></script>
        <?php
    }

    /**
     * @param array $eventTypes
     */
    public static function renderSetType(){
        echo '<form method="POST">
        <div class="bs-callout bs-callout-primary bs-callout-custom bc-dash">
        <h4>'.self::DE_TITLE_NEW.'</h4>';
        if(empty(self::$eventTypes)){
            echo '<div class="callout-content row">';
            Debug::e("Keine Daten gefunden");
        }else
            foreach(self::$eventTypes as $type){
                $icon = Html::createFaImg(["type"=>"fw","class"=>$type["iconClass"]]);
                $bsColor = isset($type["bsColor"]) ? " btn-".$type["bsColor"] : " btn-primary";
                $data = [
                    "title"=>$icon.$type["name_i18n"],
                    "descr"=>$type["description_i18n"],
                    "content"=>'<button class="btn btn-lg'.$bsColor.' pull-right col-xs-6" type="submit" name="type" value="'.$type["typeID"].'">'.$type["name_i18n"].'</button>',
                ];
                echo '<div class="callout-content row">';
                echo Html::createSettingsRowParam($data);
                echo '</div>';
            }
        echo '</div></form>';
    }

    const TMP_ITEM_MENU = '<div class="';
    const TMP_EVENT_MAP = '<div class="callout-content row event-item">
        <div class="media">
            <div class="media-left">
                <input type="hidden" name="event[maps][{{index}}][id]" value="{{mapID}}"/>
                <div class="img-thumbnail">
                    <img src="{{image}}" alt="map"/>
                </div>
            </div>
            <div class="media-body">
                <input type="hidden" name="event[maps][{{index}}][mode]" value="{{modeID}}"/>
                <h4>{{mapName}}</h4>
                <small>{{modeName}}</small>
            </div>
        </div>
        <div class="callout-buttons">
            <span class="bt bt-delete"><i class="fa fa-2x fa-times"></i></span>
        </div>
    </div>';
    const TMP_EVENT_PRICE = '<div class="callout-content row price-row event-item">
        <div class="media">
            <div class="media-body">
                <input type="hidden" name="event[prices][{{index}}][from]" value="{{from}}"/>
                <input type="hidden" name="event[prices][{{index}}][to]" value="{{to}}"/>
                <input type="hidden" name="event[prices][{{index}}][gold]" value="{{gold}}"/>
                <input type="hidden" name="event[prices][{{index}}][others]" value="{{others}}"/>
                <div class="col-md-6">
                    <h4>Rang</h4>
                    <small>Von: {{from}}</small>
                    <small>Bis: {{to}}</small>
                </div>
                <div class="col-md-6">
                    <h4>Gewinn</h4>
                    <small>Gold: {{gold}}</small>
                    <small>Andere: {{others}}</small>
                </div>
            </div>
        </div>
        <div class="callout-buttons">
            <span class="bt bt-delete"><i class="fa fa-2x fa-times"></i></span>
        </div>
    </div>';

}