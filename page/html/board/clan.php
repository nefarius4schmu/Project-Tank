<?php
/**
* Project Tank Webpage
* webpage to get information about current clan
* 
* TODO
* - move load table data to js
* - sort table
* - markable, sortable
* - add percentage, dotted
* - fixed header on scroll
* 
* @author Steffen Lange
*/
if(!isset($_page)) exit();
/* ===================================================================================== */
//_lib("WotData");
//_lib("WotHandler");
//_lib("Calc");
//_lib("Html");
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$_player = $wotUser["player"];
//$wh = new WotHandler(new WotData());
//$clan = $playerInfo->getClan();
/* get clan member data ================================================================ */
//$members = $wh->getClanMemberStats($clan["id"]);

/* ===================================================================================== */
?>
<!--<link rel="stylesheet" type="text/css" href="css/clan.css"/>-->
<div class='page-wrapper'>
    <h2><?=$_player->getClanTag();?></h2>
    <h3><?=$_player->getClanName();?></h3>
    <div class='board-wrapper'>
    <?php
    //	Debug::v($clan);
    //	Debug::v($members);
    ?>
        <div id='membersTable' class='stats-table hidden' data-loader='#loader_membersTable'><table>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <colgroup></colgroup>
            <thead><tr>
                <th data-sort='name'><span class='icon-title'><i class='fa fa-users'></i>Mitglieder</span></th>
                <th data-sort='role_i18n'><span class='icon-title'><i class='fa fa-star'></i>Rang</span></th>
                <th data-sort='global'><span class='icon-title'><i class='fa fa-globe'></i>Pers&ouml;nliche Wertung</span></th>
                <th data-sort='battles'><span class='icon-title'><i class='fa fa-bomb'></i>Gefechte</span></th>
                <th data-sort='wins'><span class='icon-title'><i class='fa fa-trophy'></i>Gewonnen</span></th>
                <th data-sort='winRatePerBattle'><span class='icon-title'><i class='fa fa-trophy'></i>Siege/Niederlagen</span></th>
                <th data-sort='shots'><span class='icon-title'><i class='fa fa-f fa-certificate'></i>Sch&uuml;sse</span></th>
                <th data-sort='hits'><span class='icon-title'><i class='fa fa-bullseye'></i>Treffer</span></th>
                <th data-sort='avgHitRatePerBattle'><span class='icon-title'><i class='fa fa-bullseye'></i>Treffer pro Gefecht</span></th>
                <th data-sort='damage'><span class='icon-title'><i class='fa fa-bomb'></i>Schaden</span></th>
                <th data-sort='avgDamagePerBattle'><span class='icon-title'><i class='fa fa-bomb'></i>Schaden pro Gefecht</span></th>
            </tr></thead>
            <tbody id='membersTableBody'></tbody>
            <tfoot></tfoot>
        </table></div>
        <div class='stats-table fixed-top hidden'><table>
            <colgroup>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
                <col class='sortable'/>
            </colgroup>
            <thead><tr>
                <th data-sort='name'><span class='icon-title'><i class='fa fa-users'></i>Mitglieder</span></th>
                <th data-sort='role_i18n'><span class='icon-title'><i class='fa fa-star'></i>Rang</span></th>
                <th data-sort='global'><span class='icon-title'><i class='fa fa-globe'></i>Pers&ouml;nliche Wertung</span></th>
                <th data-sort='battles'><span class='icon-title'><i class='fa fa-bomb'></i>Gefechte</span></th>
                <th data-sort='wins'><span class='icon-title'><i class='fa fa-trophy'></i>Gewonnen</span></th>
                <th data-sort='winRatePerBattle'><span class='icon-title'><i class='fa fa-trophy'></i>Siege/Niederlagen</span></th>
                <th data-sort='shots'><span class='icon-title'><i class='fa fa-f fa-certificate'></i>Sch&uuml;sse</span></th>
                <th data-sort='hits'><span class='icon-title'><i class='fa fa-bullseye'></i>Treffer</span></th>
                <th data-sort='avgHitRatePerBattle'><span class='icon-title'><i class='fa fa-bullseye'></i>Treffer pro Gefecht</span></th>
                <th data-sort='damage'><span class='icon-title'><i class='fa fa-bomb'></i>Schaden</span></th>
                <th data-sort='avgDamagePerBattle'><span class='icon-title'><i class='fa fa-bomb'></i>Schaden pro Gefecht</span></th>
            </tr></thead>
        </table></div>
        <div class='center-wrapper'>
            <div id='loader_membersTable' class='loader loader-fa'>
                <i class='fa fa-cog fa-2x fa-fw fa-spin'></i>
                <small>Statistiken werden geladen..</small>
            </div>
            <div id='message_membersTable' class='message error hidden'>Fehler beim Laden!</div>
        </div>
    </div>
</div>