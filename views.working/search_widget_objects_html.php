<?php

    $vn_pos = $this->getVar('pos');
    $vb_showallresults = $this->getVar('showallresults');

    if(is_array($_COOKIE)) {
        //var_dump($_COOKIE);
    }
    $vs_search_page_start = $_COOKIE["simpleEditorObjectsSearchStart"];
    $vs_search_page_end = $_COOKIE["simpleEditorObjectsSearchEnd"];
    $vs_request_idno = $_COOKIE["simpleEditorObjectsSearchIdno"];
    $vs_request_tous_champs = $_COOKIE["simpleEditorObjectsSearchTousChamps"];
    $vs_request_localisation = $_COOKIE["simpleEditorObjectsSearchLocalisation"];
    $vs_request_datation = $_COOKIE["simpleEditorObjectsSearchDatation"];
    $vs_request_technique = $_COOKIE["simpleEditorObjectsSearchTechnique"];
    $vs_request_titre = $_COOKIE["simpleEditorObjectsSearchTitre"];
    $vs_request_auteur = $_COOKIE["simpleEditorObjectsSearchAuteur"];
    $vs_request_domaine = $_COOKIE["simpleEditorObjectsSearchDomaine"];

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
    <input name="search-localisation" id="leftSearchResult-form-search-localisation" <?php print($vs_request_localisation ? "value=\"$vs_request_localisation\"" : ""); ?> type="text" placeholder="Localisation" title="Localisation">
    <input name="search-datation" id="leftSearchResult-form-search-datation" <?php print($vs_request_datation ? "value=\"$vs_request_datation\"" : ""); ?> type="text" placeholder="Datation" title="Datation">
    <input name="search-technique" id="leftSearchResult-form-search-technique" <?php print($vs_request_technique ? "value=\"$vs_request_technique\"" : ""); ?> type="text" placeholder="Technique" title="Technique">
    <input name="search-titre" id="leftSearchResult-form-search-titre" <?php print($vs_request_titre ? "value=\"$vs_request_titre\"" : ""); ?> type="text" placeholder="Titre" title="Titre">
    <input name="search-auteur" id="leftSearchResult-form-search-auteur" <?php print($vs_request_auteur ? "value=\"$vs_request_auteur\"" : ""); ?> type="text" placeholder="Auteur" title="Auteur">
    <input name="search-domaine" id="leftSearchResult-form-search-domaine" <?php print($vs_request_domaine ? "value=\"$vs_request_domaine\"" : ""); ?> type="text" placeholder="Domaine" title="Domaine">
    <input name="search-idno" id="leftSearchResult-form-search-idno" <?php print($vs_request_idno ? "value=\"$vs_request_idno\"" : ""); ?> type="text" placeholder="Numéro d'inventaire" title="Numéro d'inventaire">
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
                url: "<?php print __CA_URL_ROOT__;?>/index.php/simpleEditor/Objects/DoSearch/showallresults/<?php print $vb_showallresults; ?>  ",
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