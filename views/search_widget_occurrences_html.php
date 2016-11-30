<?php

    $vn_pos = $this->getVar('pos');
    $vb_showallresults = $this->getVar('showallresults');
    $vn_type_id = $this->getVar('type_id');
	$vs_cookie_prefix= $this->getVar('cookie_prefix');

	//var_dump($vn_type_id);die();
    if(is_array($_COOKIE)) {
        //var_dump($_COOKIE);
    }
    $vs_search_page_start = $_COOKIE["simpleEditorOccurrencesSearchStart"];
    $vs_search_page_end = $_COOKIE["simpleEditorOccurrencesSearchEnd"];
    //$vs_request_tous_champs = $_COOKIE["simpleEditorOccurrencesSearchTousChamps"];
	$vs_request_tous_champs = $_COOKIE[$vs_cookie_prefix."_".$vn_type_id."_TousChamps"];

    $vs_request_titre = $_COOKIE["simpleEditorOccurrencesSearchTitre"];

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
<?php if($vn_type_id != 133) : ?>
    <input name="search-titre" id="leftSearchResult-form-search-titre" <?php print($vs_request_titre ? "value=\"$vs_request_titre\"" : ""); ?> type="text" placeholder="Titre" title="Titre">
<?php endif; ?>
    <a href="#" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button">
            Recherche
        </span>
    </a>
    <a href="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/Occurrences/ClearSearch<?php if($vn_type_id) print "/Type/".$vn_type_id; ?>" id="leftSearchResult-icon" class="form-button 1457293322">
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
                url: "<?php print __CA_URL_ROOT__;?>/index.php/simpleEditor/Occurrences/DoSearch/type_id/<?php print $vn_type_id; ?>/showallresults/<?php print $vb_showallresults; ?>  ",
                type: "POST",
                data: jQuery("form#leftSearchResult-form").serialize(),
                dataType: 'html', // JSON
                success: function (html) { // Je récupère la réponse du fichier PHP
                    jQuery("#leftSearchResults").hide();
                    jQuery("#leftSearchResults").html(html);
                    jQuery("#leftSearchResults").fadeIn();
                    jQuery(document).ready(function () {
//                        jQuery("#leftSearchResults").jscroll({
//                            debug: true,
//                            padding: 5,
//                            loadingHtml: '<small>...</small>'
//                        });
                    });
                }
            });
        };

		leftSearchResultRun();
		
        jQuery("#leftSearchResult-icon").on('click', leftSearchResultRun);

        jQuery("#leftSearchResult-form input").keypress(function(e) {
            if (e.which == 13) {
		        e.preventDefault();
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