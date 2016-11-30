<?php

	require_once(__CA_MODELS_DIR__."/ca_lists.php");
	require_once(__CA_MODELS_DIR__."/ca_list_items.php");
	require_once(__CA_MODELS_DIR__."/ca_list_item_labels.php");

    // Using a generic ca_lists to fetch back content with getItemsForList(list_id)
	$vt_list = new ca_lists();

    $vn_pos = $this->getVar('pos');
    $vb_showallresults = $this->getVar('showallresults');

    if(is_array($_COOKIE)) {
	    print "<!-- ";
        var_dump($_COOKIE);
        print "-->";
    }
    $vs_search_page_start = $_COOKIE["simpleEditorObjectsSearchStart"];
    $vs_search_page_end = $_COOKIE["simpleEditorObjectsSearchEnd"];
    $vs_request_idno = $_COOKIE["simpleEditorObjectsSearchIdno"];
    $vs_request_tous_champs = $_COOKIE["simpleEditorObjectsSearchTousChamps"];
    $vs_request_localisation = $_COOKIE["simpleEditorObjectsSearchLocalisation"];
    $vs_request_datation = $_COOKIE["simpleEditorObjectsSearchDatation"];
    $vs_request_technique = $_COOKIE["simpleEditorObjectsSearchTechnique"];
	$vs_request_materiau = $_COOKIE["simpleEditorObjectsSearchMateriau"];
    //var_dump($vs_request_materiau);die();
    $vs_request_titre = $_COOKIE["simpleEditorObjectsSearchTitre"];
    if(trim(vs_request_titre) == "") {
        var_dump(trim($vs_request_titre));die();
    }
    $vs_request_auteur = $_COOKIE["simpleEditorObjectsSearchAuteur"];
    $vs_request_domaine = $_COOKIE["simpleEditorObjectsSearchDomaine"];


	//var_dump($vt_materiaux_list->getItemsForList(162));	var_dump();
    $va_materiaux_items = $vt_list->getItemsForList(161);
    $va_list_materiaux = array();
    $va_list_materiaux[] = array("item_id"=>"", "name_singular"=>"Matériau", "name_plural"=>"Matériaux");
    foreach($va_materiaux_items as $va_materiau_item) {
        $va_materiau = reset($va_materiau_item);
        $va_list_materiaux[] = array("item_id"=>$va_materiau["item_id"], "name_singular"=>"&nbsp;&nbsp;".$va_materiau["name_singular"], "name_plural"=>"&nbsp;&nbsp;".$va_materiau["name_plural"]);
    }

    $va_techniques_items = $vt_list->getItemsForList(160);
    $va_list_techniques = array();
    $va_list_techniques[] = array("item_id"=>"", "name_singular"=>"Technique", "name_plural"=>"Techniques");
    foreach($va_techniques_items as $va_technique_item) {
        $va_technique = reset($va_technique_item);
        $va_list_techniques[] = array("item_id"=>$va_technique["item_id"], "name_singular"=>"&nbsp;&nbsp;".$va_technique["name_singular"], "name_plural"=>"&nbsp;&nbsp;".$va_technique["name_plural"]);
    }

	$va_domaines_items = $vt_list->getItemsForList(7, array("enabledOnly"=>true));
    $va_list_domaines = array();
    $va_list_domaines[] = array("item_id"=>"", "name_singular"=>"Type d'objet", "name_plural"=>"Type d'objet");
    foreach($va_domaines_items as $va_domaine_item) {
        $va_domaine = reset($va_domaine_item);
        $va_list_domaines[] = array("item_id"=>$va_domaine["item_id"], "name_singular"=>"&nbsp;&nbsp;".$va_domaine["name_singular"], "name_plural"=>"&nbsp;&nbsp;".$va_domaine["name_plural"]);
    }

	$va_list_localisations = array(
		array("label"=>"Localisation", code=>""),	
		array("label"=>"&nbsp;&nbsp;Villa", code=>"V"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Niveau 1", code=>"V1"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Niveau 2", code=>"V2"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Niveau 3", code=>"V3"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Niveau 6", code=>"V6"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Niveau 9", code=>"V9"),	
		array("label"=>"&nbsp;&nbsp;Bâtiments du jardin", code=>"BJ"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Bibliothèque annexe", code=>"BJ-BA"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Bureau du Patrimoine", code=>"BJ-BP"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;San Vittorio", code=>"BJ-SV"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Gypsothèque", code=>"BJ-G"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Atelier 3", code=>"BJ-A3"),	
		array("label"=>"&nbsp;&nbsp;Réserve", code=>"R"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Réserve - Tirages en plâtre", code=>"R-TP"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Grottone", code=>"R-G"),	
		array("label"=>"&nbsp;&nbsp;Jardin", code=>"J"),
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Bosco", code=>"J-B"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Parterre", code=>"J-P"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Carrés", code=>"J-C"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Porte Vignole", code=>"J-PV"),	
		array("label"=>"&nbsp;&nbsp;&nbsp;&nbsp;Allée des orangers", code=>"J-AO")	
	);

    $vb_search_to_launch = false;
    if ($vs_request_idno || $vs_request_localisation || $vs_request_datation || $vs_request_technique || $vs_request_titre || $vs_request_auteur || $vs_request_domaine) {
        $vb_search_to_launch = true;
    }

?>
<h3 style="padding-top:10px;font-size:1.1em;">Objet</h3>
<form id="leftSearchResult-form" action="" method="post">

    <input name="pos" id="leftSearchResult-form-pos" <?php print($vn_pos ? "value=\"$vn_pos\"" : ""); ?> type="hidden">
    <input name="search-start" id="leftSearchResult-form-search-start" <?php print($vs_search_page_start ? "value=\"$vs_search_page_start\"" : ""); ?> type="hidden">
    <input name="search-end" id="leftSearchResult-form-search" <?php print($vs_search_page_end ? "value=\"$vs_search_page_end\"" : ""); ?> type="hidden">
    <input name="search-tous-champs" id="leftSearchResult-form-search-tous-champs" <?php print($vs_request_tous_champs ? "value=\"$vs_request_tous_champs\"" : ""); ?> type="text" placeholder="Tous champs" title="Tous champs">
    <!--<input name="search-localisation" id="leftSearchResult-form-search-localisation" <?php print($vs_request_localisation ? "value=\"$vs_request_localisation\"" : ""); ?> type="text" placeholder="Localisation" title="Localisation">-->
    <?php 
	    print "<select name=\"search-localisation\" class=\"localisationSelect\">";
        foreach($va_list_localisations as $va_localisation_select_item) {
        	print "<option value=\"".$va_localisation_select_item["code"]."\" $selected>".$va_localisation_select_item["label"]."</option>";
        }
        print "</select>";
        ?>

    <input name="search-datation" id="leftSearchResult-form-search-datation" <?php print($vs_request_datation ? "value=\"$vs_request_datation\"" : ""); ?> type="text" placeholder="Datation" title="Datation">
    <?php print "
    <select name=\"search-materiau\" class=\"materiauxSelect\">";
        foreach($va_list_materiaux as $va_materiau_select_item) {
        print "<option value=\"".$va_materiau_select_item["item_id"]."\" ".($va_materiau_select_item["item_id"] == $vs_request_materiau? " selected=\"selected\" " : "" ).">".$va_materiau_select_item["name_singular"]."</option>";
        }
        print "
    </select>
    ";
    print "
    <select name=\"search-technique\" class=\"techniquesSelect\">";
        foreach($va_list_techniques as $va_technique_select_item) {
            print "<option value=\"".$va_technique_select_item["item_id"]."\" ".($va_technique_select_item["item_id"] == $vs_request_technique ? " selected=\"selected\" " : "" ).">".$va_technique_select_item["name_singular"]."</option>";
        }
        print "
    </select>
    ";

?>
    <!-- <input name="search-technique" id="leftSearchResult-form-search-technique" <?php print($vs_request_technique ? "value=\"$vs_request_technique\"" : ""); ?> type="text" placeholder="Technique" title="Technique"> -->

    <input name="search-titre" id="leftSearchResult-form-search-titre" <?php print($vs_request_titre ? "value=\"$vs_request_titre\"" : ""); ?> type="text" placeholder="Titre" title="Titre">
    <input name="search-auteur" id="leftSearchResult-form-search-auteur" <?php print($vs_request_auteur ? "value=\"$vs_request_auteur\"" : ""); ?> type="text" placeholder="Auteur" title="Auteur">
<?php
    print "
    <select name=\"search-domaine\" class=\"domainesSelect\">";
        foreach($va_list_domaines as $va_domaine_select_item) {
	        if($va_domaine_select_item["item_id"] != $vs_request_domaine) {
			    print "<option value=\"".$va_domaine_select_item["item_id"]."\">".$va_domaine_select_item["name_singular"]."</option>";    
	        } else {
				print "<option value=\"".$va_domaine_select_item["item_id"]."\" selected=\"selected\">".$va_domaine_select_item["name_singular"]."</option>";	        
	        }
            //print "<option value=\"".$va_domaine_select_item["item_id"]."\">".$va_domaine_select_item["name_singular"]."</option>";
        }
        print "
    </select>
    ";
    ?>
    <input name="search-idno" id="leftSearchResult-form-search-idno" <?php print($vs_request_idno ? "value=\"$vs_request_idno\"" : ""); ?> type="text" placeholder="Numéro d'inventaire" title="Numéro d'inventaire">
    <a href="#" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button">
            Recherche
        </span>
    </a>
    <a href="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/Objects/ClearSearch" id="leftSearchResult-icon" class="form-button 1457293322">
        <span class="form-button">
            Vider
        </span>
    </a>
    <a href="<?php print __CA_URL_ROOT__; ?>/index.php/find/SearchObjectsAdvanced/Index/reset" id="leftSearchResult-icon" class="form-button 1457293322">
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
                url: "<?php print __CA_URL_ROOT__;?>/index.php/simpleEditor/Objects/DoSearch/showallresults/<?php print $vb_showallresults; ?>  ",
                type: "POST",
                data: jQuery("form#leftSearchResult-form").serialize(),
                dataType: 'html', // JSON
                success: function (html) { // Je récupère la réponse du fichier PHP
                    jQuery("#leftSearchResults").hide();
                    jQuery("#leftSearchResults").delay(800).html(html).fadeIn();
                    jQuery(document).ready(function () {
                        jQuery("#leftSearchResults").jscroll({
                            debug: true,
                            padding: 5,
                            loadingHtml: '<small>...</small>'
                        });
                    });
                    return(html);
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