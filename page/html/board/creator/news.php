<?php
/**
 * Project Tank Webpage
 * creator webpage for makings news entries
 * @author Steffen Lange
 */
if(!isset($_page)) exit();
$debug = false;
_lib("DB");
_lib("DBHandler");
/* ===================================================================================== */
$wotUser = $_page["user"];
/** @var WotPlayer $player */
$player = $wotUser["player"];
/* ===================================================================================== */
$dbh = new DBHandler(DB::getLink(DB::DB_PTWG));
$dbh->debug($debug);
/* ===================================================================================== */
$uid = isset($_GET["uid"]) ? $_GET["uid"] : null;
$isEdit = !empty($uid);
$news = $isEdit ? $dbh->getNewsByUid($uid) : null;
$isNews = !empty($news) && $news["userID"] == $player->getID();
/* ===================================================================================== */
$canEdit = $isEdit && $news["userID"] == $player->getID();
$canEdit = $canEdit && (!isset($news["clanID"]) || ($player->hasClan() && $player->getClanID() == $news["clanID"]) );
$isNews = $isNews && $canEdit;
/* ===================================================================================== */
$title = $isNews ? $news["title"] : null;
$text = $isNews ? $news["text"] : null;
$summary = $isNews ? $news["summary"] : null;
/* ===================================================================================== */
if($isEdit && !$canEdit) exit("<p>Sie sind nicht berechtigt, diesen Beitrag zu bearbeiten!</p>");
else if($isEdit && !$isNews) exit("<p>Fehler beim Bearbeiten. Versuchen Sie es zu einem anderen Zeitpunkt erneut.</p>");
/* ===================================================================================== */
$result = $dbh->getNewsCategories();
$newsCats = $dbh->parseArray($result);
/* ===================================================================================== */
$i18nHeadline = $isEdit ? "Beitrag bearbeiten" : "Beitrag erstellen";
$i18nSaveNewsButton = $isEdit ? "Speichern" : "Erstellen";
/* ===================================================================================== */
?>
<div class='page-wrapper'>
    <h2><?=$i18nHeadline?></h2>
    <form action='<?=URL_ROOT.ROUTE_NEWS."/".ROUTE_POST;?>' method='post'>
        <?=($isEdit?'<input type="hidden" name="news[id]" value="'.$news["newsID"].'"':null)?>
        <div class='creator'>
            <div class='form-group'>
                <label for='edPostTitle'>Titel</label>
                    <input type='text' id='edPostTitle' class='form-control' name='news[title]' maxlength='40' placeholder='Titel..' value="<?=$title?>"/>
            </div>
            <div class='form-group'>
                <label for='edPostText'>Beitrag</label>
                <textarea id='edPostText' name='news[text]' class='ckeditor form-control'><?=$text?></textarea>
            </div>
            <div class='form-group'>
                <label for='edPostSummary'>Zusammenfassung (optional)</label>
                <textarea id='edPostSummary' name='news[summary]' class='form-control' maxlength="250"><?=$summary?></textarea>
            </div>
            <h2>Allgemeine Einstellungen</h2>
            <div class='form-group'>
                <label for='edPostCat'>Kategorie</label>
                <select id='edPostCat' name='news[cat]' class='form-control'>
                    <?php
                    foreach($newsCats as $cat) {
                        if(!$player->hasClan() && $cat["clanOnly"] == "1") continue;

                        $selected = Html::isget($news["catID"], $cat["catID"], " selected");
                        echo '<option value="'.$cat["catID"].'"'.$selected.'>'.Html::getNewsCatLang($cat["name"]).'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class='form-group'>
                <label for='edPostCoverImage'>Titelbild</label>
                <select id='edPostCoverImage' name='news[cover]' class='form-control hidden' data-selected="<?=$news["coverimage"]?>"></select>
                <small id="edPostCoverImage_noImage">Keine Bilder gefunden</small>
            </div>
        </div>
        <div class='row button-bar'>
            <a href='<?=URL_ROOT.ROUTE_NEWS?>' class='btn btn-danger pull-left'><i class='fa fa-fw fa-times'></i>Abbrechen</a>
            <button type='submit' class='btn btn-success pull-right'><i class='fa fa-fw fa-plus'></i><?=$i18nSaveNewsButton?></button>
        </div>
    </form>
</div>