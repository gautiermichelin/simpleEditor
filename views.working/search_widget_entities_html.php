<?php

    $vn_pos = $this->getVar('pos');
    $vb_showallresults = $this->getVar('showallresults');

    if(is_array($_COOKIE)) {
        //var_dump($_COOKIE);
    }
    $vs_search_page_start = $_COOKIE["simpleEditorEntitiesSearchStart"];
    $vs_search_page_end = $_COOKIE["simpleEditorEntitiesSearchEnd"];
    $vs_request_tous_champs = $_COOKIE["simpleEditorEntitiesSearchTousChamps"];
    $vs_request_titre = $_COOKIE["simpleEditorEntitiesSearchTitre"];

    $vb_search_to_launch = false;
    if ($vs_request_idno || $vs_request_localisation || $vs_request_datation || $vs_request_technique || $vs_request_titre || $vs_request_auteur || $vs_request_domaine) {
        $vb_search_to_launch = true;
    }

?>

<form id="leftSearchResult-form" action="" method="post">

    <input name="pos" id="leftSearchResult-form-pos" <?php print($vn_pos ? "value=\"$vn_pos\"" : ""); ?> type="hidden">
    <input name="search-start" id="leftSearchResult-form-search-start" <?php print($vs_search_page_start ? "value=\"$vs_search_page_start\"" : ""); ?> type="hidden">
    <input name="search-end" id="leftSearchResult-form-search" <?php print($vs_search_page_end ? "value=\"$vs_search_page_end\"" : ""); ?> type="hidden">
    <input name="search-tous-champs" id="leftSearchResult-form-search-tous-champs" <?php print($vs_request_tous_champs ? "value=\"$vs_request_tous_champs\"" : ""); ?> type="text" placeholder="Tous champs" title="Tous champs">
    <input name="search-titre" id="leftSearchResult-form-search-titre" <?php print($vs_request_titre ? "value=\"$vs_request_titre\"" : ""); ?> type="text" placeholder="Nom" title="Nom">
    <a href="#" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button">
            <img src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_027_search.png" border="0" class="form-button-left" style="padding-right: 10px" data-pin-nopin="true">
            Recherche
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
                url: "<?php print __CA_URL_ROOT__;?>/index.php/simpleEditor/Entities/DoSearch/showallresults/<?php print $vb_showallresults; ?>  ",
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