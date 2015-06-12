<?php
/**
* Project Tank Webpage
* webpage for user settings
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
$debug = false;
/* ===================================================================================== */
_def("settings");
_lib("DB");
_lib("DBHandler");
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
if($dbh === false) _error(ERROR_DB_CONNECTION, null, $debug);
/* ===================================================================================== */
$wotUser = $_page["user"];
/** @var WotPlayer $playerInfo */
$playerInfo = $wotUser["player"];
$hasClan = $playerInfo->hasClan();
$clanLogoMedium = $hasClan ? $playerInfo->getClanEmblemMedium() : PATH_ICON_MEMBERS;
/* ===================================================================================== */
$settings = $dbh->getUserSettings($playerInfo->getID());
//Debug::v($settings);
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <form action='<?=URL_ROOT.ROUTE_SET_SETTINGS;?>' method='POST'>
<!--    <form action='--><?//=URL_ROOT.ROUTE_SETTINGS."/".ROUTE_SET;?><!--' method='POST'>-->
        <h2><?=PAGE_TITLE_SETTINGS;?></h2>
        <div class='button-bar bar-top'>
            <button type='submit' class='btn btn-success btn-big pull-right'>Anwenden</button>
        </div>
        <div class='bs-callout bs-callout-primary bs-callout-custom'>
            <h4>Anzeige</h4>
            <div class='callout-content'><?php
                $options = [
                    "title"=>TEXT_SETTINGS_USE_THEME,
                    "descr"=>TEXT_SETTINGS_USE_THEME_DESCR,
                    "elements"=>[
                        "Ja"=>1,
                        "Nein"=>0,
                    ],
                    "active"=>$settings[SETTINGS_ID_THEME_CLAN_COLOR],
                    "input"=>"settings[".SETTINGS_ID_THEME_CLAN_COLOR."]",
                ];
                echo Html::createSwitchButton("useTheme", $options, true);
            ?></div>
        </div>
        <div class='bs-callout bs-callout-danger bs-callout-custom'>
            <h4>Private Daten</h4>
            <div class='callout-content'><?php

                $options = [
                    "title"=>TEXT_SETTINGS_TANKS,
                    "descr"=>TEXT_SETTINGS_TANKS_DESCR,
                    "active"=>$settings[SETTINGS_ID_SHOW_TANKS],
                    "input"=>"settings[".SETTINGS_ID_SHOW_TANKS."]",
                    "elements"=>[
                        [
                            "text"=>"Nicht anzeigen",
                            "value"=>0,
                            "data"=>[
                                "imagesrc"=>PATH_ICON_LOCK,//"images/icons/settings/lock-32.png",
                                "description"=>"Panzer verbergen",
                            ],
                        ],
                        [
                            "text"=>"Clan F&uuml;hrung",
                            "value"=>1,
                            "data"=>[
                                "imagesrc"=>PATH_ICON_SINGLE,//"images/icons/settings/contacts-32.png",
                                "description"=>"nur für Clan-Führung sichtbar",
                            ],
                        ],
                        [
                            "text"=>"Offiziere",
                            "value"=>2,
                            "data"=>[
                                "imagesrc"=>PATH_ICON_GROUP,//"images/icons/settings/group-32.png",
                                "description"=>"für Ränge ab Junior Offizier sichtbar",
                            ],
                        ],
                        [
                            "text"=>"Soldaten",
                            "value"=>3,
                            "data"=>[
                                "imagesrc"=>PATH_ICON_MEMBERS,
                                "description"=>"für Ränge ab Soldat sichtbar",
                            ],
                        ],
                        [
                            "text"=>"Clan",
                            "value"=>4,
                            "data"=>[
                                "fa"=>"fa-users",
                                "imagesrc"=>$clanLogoMedium,
                                "description"=>"für alle Mitglieder des Clans sichtbar",
                            ],
                        ],
                    ],
                ];
                echo Html::createDataSelect("showTanks", $options, true);
            ?>
            </div>
        </div>
        <div class='button-bar bar-bottom'>
            <button type='submit' class='btn btn-success btn-big pull-right'>Anwenden</button>
        </div>
        <!--<div class='bs-callout bs-callout-custom'>
            <h4>Global</h4>
            <div class='callout-content'>content</div>
        </div>
        <div class='bs-callout bs-callout-success bs-callout-custom'>
            <h4>Schmu</h4>
            <div class='callout-content'>content</div>
        </div>
        <div class='bs-callout bs-callout-warning bs-callout-custom'>
            <h4>Noch mehr Schmu</h4>
            <div class='callout-content'>content</div>
        </div>-->
        <!--<div class='button-bar'>
            <button type='submit' class='btn btn-success btn-big pull-right'>Anwenden</button>
        </div>-->
    </form>
</div>

