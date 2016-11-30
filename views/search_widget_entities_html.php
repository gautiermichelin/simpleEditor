<?php

    $vn_pos = $this->getVar('pos');
    $vn_type_id = $this->getVar('type_id');
	$vb_showallresults = $this->getVar('showallresults');
	$vs_cookie_prefix= $this->getVar('cookie_prefix');

    if(is_array($_COOKIE)) {
        //var_dump($_COOKIE);
    }
    $vs_search_page_start = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_Start"];
    $vs_search_page_end = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_End"];

    $vs_request_tous_champs = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_TousChamps"];
    $vs_request_surname = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_Surname"];
    $vs_request_forename = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_Forename"];
	$vs_request_birth = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_Birth"];
	$vs_request_death = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_Death"];

    $vb_search_to_launch = false;
    if ($vs_request_idno || $vs_request_localisation || $vs_request_datation || $vs_request_technique || $vs_request_titre || $vs_request_auteur || $vs_request_domaine) {
        $vb_search_to_launch = true;
    }

?>
<h3 style="padding-top:10px;font-size:1.1em;"><?php print caGetListItemByIDForDisplay($vn_type_id,true); ?></h3>
<form id="leftSearchResult-form" action="" method="post">
	<input name="type_id" id="leftSearchResult-form-type-id" <?php print($vn_type_id ? "value=\"$vn_type_id\"" : ""); ?> type="hidden">
    <input name="pos" id="leftSearchResult-form-pos" <?php print($vn_pos ? "value=\"$vn_pos\"" : ""); ?> type="hidden">
    <input name="search-start" id="leftSearchResult-form-search-start" <?php print($vs_search_page_start ? "value=\"$vs_search_page_start\"" : ""); ?> type="hidden">
    <input name="search-end" id="leftSearchResult-form-search" <?php print($vs_search_page_end ? "value=\"$vs_search_page_end\"" : ""); ?> type="hidden">
    <input name="search-tous-champs" id="leftSearchResult-form-search-tous-champs" <?php print($vs_request_tous_champs ? "value=\"$vs_request_tous_champs\"" : ""); ?> type="text" placeholder="Tous champs" title="Tous champs">
	<input name="search-surname" id="leftSearchResult-form-search-surname" <?php print($vs_request_surname ? "value=\"$vs_request_surname\"" : ""); ?> type="text" placeholder="Nom" title="Nom">

<?php if ($vn_type_id == 107) : ?>	
	<input name="search-forename" id="leftSearchResult-form-search-forename" <?php print($vs_request_forename ? "value=\"$vs_request_forename\"" : ""); ?> type="text" placeholder="Prénom" title="Prénom">
	<input name="search-birth" id="leftSearchResult-form-search-birth" <?php print($vs_request_birth ? "value=\"$vs_request_birth\"" : ""); ?> type="text" placeholder="Date de naissance" title="Date de naissance">
 	<input name="search-death" id="leftSearchResult-form-search-death" <?php print($vs_request_death ? "value=\"$vs_request_death\"" : ""); ?> type="text" placeholder="Date de mort" title="Date de mort">
	<input name="search-concours" id="leftSearchResult-form-search-concours" <?php print($vs_request_forename ? "value=\"$vs_request_concours\"" : ""); ?> type="text" placeholder="Prix de Rome/concours" title="Prix de Rome/concours">
	<input name="search-pensionnaire_arrivee" id="leftSearchResult-form-search-pensionnaire_arrivee" <?php print($vs_request_forename ? "value=\"$vs_request_pensionnaire_arrivee\"" : ""); ?> type="text" placeholder="Pensionnaire : date d'arrivée" title="Pensionnaire : date d'arrivée">
	<input name="search-pensionnaire_depart" id="leftSearchResult-form-search-pensionnaire_depart" <?php print($vs_request_forename ? "value=\"$vs_request_pensionnaire_depart\"" : ""); ?> type="text" placeholder="Pensionnaire : date de départ" title="Pensionnaire : date de départ">

<?php endif; ?> 	
<?php if ($vn_type_id == 108) : ?>
	<input name="search-ville" id="leftSearchResult-form-search-ville" <?php print($vs_request_forename ? "value=\"$vs_request_ville\"" : ""); ?> type="text" placeholder="Ville" title="Ville">

<?php endif; ?>
    <a href="#" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button">
            Recherche
        </span>
    </a>
    <a href="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/Entities/ClearSearch<?php if($vn_type_id) print "/Type/".$vn_type_id; ?>" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button">
            Vider
        </span>
    </a>
    <a href="<?php print __CA_URL_ROOT__; ?>/index.php/find/SearchEntitiesAdvanced/Index/reset/type_id/<?php print $vn_type_id; ?>" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button link-advanced-search">
            Recherche avancée
        </span>
    </a>    
    
</form>
<div class="leftSearchResultsWrapper">
    <div class="leftSearchResults" id="leftSearchResults"></div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function() {
        var leftSearchResultRun = function () {
            // Envoi de la requête HTTP en mode asynchrone
            $.ajax({
                url: "<?php print __CA_URL_ROOT__;?>/index.php/simpleEditor/Entities/DoSearch/type_id/<?php print $vn_type_id; ?>/showallresults/<?php print $vb_showallresults; ?>  ",
                type: "POST",
                data: jQuery("form#leftSearchResult-form").serialize(),
                dataType: 'html', // JSON
                success: function (html) { // Je récupère la réponse du fichier PHP
                    jQuery("#leftSearchResults").hide();
                    jQuery("#leftSearchResults").html(html);
                    jQuery("#leftSearchResults").fadeIn();
                    jQuery(document).ready(function () {
                        jQuery("#leftSearchResults").jscroll({
                            debug: true,
                            padding: 5,
                            loadingHtml: '<small>...</small>'
                        });
                    });
                }
            });
        };

		leftSearchResultRun();

        jQuery("#leftSearchResult-icon").on('click', leftSearchResultRun);

        jQuery("#leftSearchResult-form input").keypress(function(e) {
            if (e.which == 13) {
                jQuery("#leftSearchResult-icon").click();
            }
        });


        $( "#leftSearchResult-form").find("input").tooltip({
            position: { my: "left+15 center", at: "right center" }
        });

<?php
    if ($vb_search_to_launch):
    // If we already have a search, let's refresh
?>
        leftSearchResultRun();
<?php
    endif;
?>
    });
</script>