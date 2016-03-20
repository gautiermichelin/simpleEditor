<?php

    if(is_array($_COOKIE)) {
        //var_dump($_COOKIE);
    }
    $vs_request_idno = $_COOKIE["simpleEditorObjectsIdno"];
    $vs_request_tous_champs = $_COOKIE["simpleEditorObjectsTousChamps"];
    $vs_request_localisation = $_COOKIE["simpleEditorObjectsLocalisation"];
    $vs_request_datation = $_COOKIE["simpleEditorObjectsDatation"];
    $vs_request_technique = $_COOKIE["simpleEditorObjectsTechnique"];
    $vs_request_titre = $_COOKIE["simpleEditorObjectsTitre"];
    $vs_request_auteur = $_COOKIE["simpleEditorObjectsAuteur"];
    $vs_request_domaine = $_COOKIE["simpleEditorObjectsDomaine"];

    $vb_search_to_launch = false;
    if ($vs_request_idno || $vs_request_localisation || $vs_request_datation || $vs_request_technique || $vs_request_titre || $vs_request_auteur || $vs_request_domaine) {
        $vb_search_to_launch = true;
    }
?>

<form id="leftSearchResult-form" action="" method="post">
    <input name="search-tous-champs" <?php print($vs_request_tous_champs ? "value=\"$vs_request_tous_champs\"" : ""); ?> type="text" placeholder="Tous champs">
    <input name="search-localisation" <?php print($vs_request_localisation ? "value=\"$vs_request_localisation\"" : ""); ?> type="text" placeholder="Localisation">
    <input name="search-datation" <?php print($vs_request_datation ? "value=\"$vs_request_datation\"" : ""); ?> type="text" placeholder="Datation">
    <input name="search-technique" <?php print($vs_request_technique ? "value=\"$vs_request_technique\"" : ""); ?> type="text" placeholder="Technique" DISABLED>
    <input name="search-titre" <?php print($vs_request_titre ? "value=\"$vs_request_titre\"" : ""); ?> type="text" placeholder="Titre">
    <input name="search-auteur" <?php print($vs_request_auteur ? "value=\"$vs_request_auteur\"" : ""); ?> type="text" placeholder="Auteur">
    <input name="search-domaine" <?php print($vs_request_domaine ? "value=\"$vs_request_domaine\"" : ""); ?> type="text" placeholder="Domaine" DISABLED>
    <input name="search-idno" <?php print($vs_request_idno ? "value=\"$vs_request_idno\"" : ""); ?> type="text" placeholder="Numéro d'inventaire">
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
                url: "<?php print __CA_URL_ROOT__;?>/index.php/simpleEditor/Objects/DoSearch",
                type: "POST",
                data: jQuery("form#leftSearchResult-form").serialize(),
                dataType: 'html', // JSON
                success: function (html) { // Je récupère la réponse du fichier PHP
                    //console.log("recherche objet : " + html); // J'affiche cette réponse
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
                    //jQuery("#leftSearchResults").jscroll();
                }
            });
        };

        jQuery("#leftSearchResult-icon").on('click', leftSearchResultRun);
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