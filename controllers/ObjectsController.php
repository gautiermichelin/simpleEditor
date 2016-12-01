<?php
/* ----------------------------------------------------------------------
 * plugins/statisticsViewer/controllers/StatisticsController.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */

 	require_once(__CA_LIB_DIR__.'/core/TaskQueue.php');
 	require_once(__CA_LIB_DIR__.'/core/Configuration.php');
 	require_once(__CA_MODELS_DIR__.'/ca_lists.php');
 	require_once(__CA_MODELS_DIR__.'/ca_objects.php');
 	require_once(__CA_MODELS_DIR__.'/ca_object_representations.php');
 	require_once(__CA_MODELS_DIR__.'/ca_locales.php');


	require_once(__CA_LIB_DIR__."/ca/Search/ObjectSearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/ObjectSearchResult.php");

	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseController.php");
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorController.php");

	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseAjaxController.php");

	class ObjectsController extends SimpleEditorController {
 		# -------------------------------------------------------
  		protected $opo_config;		// plugin configuration file
		protected $ops_table_name = 'ca_objects';		// name of "subject" table (what we're editing)
		protected $ops_cookie_prefix = 'simpleEditorObjectsControllerLastEdited_';		// cookie prefix eventually followed by record type
		protected $ops_record_id = 'object_id';		// name of "subject" table (what we're editing)
		protected $ops_table_propername = "Objects";
		protected $ops_table_type_list = 'object_types';
		protected $ops_type_id;

		# -------------------------------------------------------
		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			parent::__construct($po_request, $po_response, $pa_view_paths);

			AssetLoadManager::register('panel');

 			if (!$this->request->user->canDoAction('can_use_simple_editor_plugin')) {
 				$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
 				return;
 			}

 			$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/simpleEditor/conf/simpleEditor.conf');
			$this->ops_table_name = 'ca_objects';		// name of "subject" table (what we're editing)
			$this->ops_cookie_prefix = 'simpleEditorObjectsControllerLastEdited_';		// cookie prefix eventually followed by record type
			$this->ops_record_id = 'object_id';		// name of "subject" table (what we're editing)
			$this->ops_table_propername = "Objects";
			$this->ops_table_type_list = 'object_types';
			$this->ops_type_id = $this->request->getParameter('type_id', pInteger);
			if(!$this->ops_type_id) {
				$this->ops_type_id=caGetDefaultItemID($this->ops_table_type_list);
			}
 		}



		public function Add($pa_options = null) {
			parent::Add($pa_options);
		}

		public function DoSearch() {

			AssetLoadManager::register('bundleableEditor');

			$vn_record_id = $this->request->getParameter($this->ops_record_id, pInteger);

			$vn_pos = $this->request->getParameter('pos', pString);
			$vb_show_all_results = $this->request->getParameter('showallresults', pString);

			//print "<!--";
			$vs_start = $this->request->getParameter('search-start', pInteger);
			//var_dump($vs_start);
			$vs_end = $this->request->getParameter('search-end', pInteger);
			//var_dump($vs_end);
			$vs_request_tous_champs = $this->request->getParameter('search-tous-champs', pString);
			//var_dump($vs_request_tous_champs);
			$vs_request_idno = $this->request->getParameter('search-idno', pString);
			//var_dump($vs_request_idno);
			$vs_request_localisation = $this->request->getParameter('search-localisation', pString);
			//var_dump($vs_request_localisation);
			$vs_request_datation = $this->request->getParameter('search-datation', pString);
			//var_dump($vs_request_datation);
			$vs_request_technique = $this->request->getParameter('search-technique', pString);
			//var_dump($vs_request_technique);
			$vs_request_materiau = $this->request->getParameter('search-materiau', pString);
			//var_dump($vs_request_materiau);
			$vs_request_titre = $this->request->getParameter('search-titre', pString);
			//var_dump($vs_request_titre);
			$vs_request_auteur = $this->request->getParameter('search-auteur', pString);
			//var_dump($vs_request_auteur);
			$vs_request_domaine = $this->request->getParameter('search-domaine', pString);
			//var_dump($vs_request_domaine);

			//print "-->";

			// Storing search form values inside a cookie
			setcookie("simpleEditor".$this->ops_table_propername."SearchPos",$vs_pos, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchStart",$vs_start, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchEnd",$vs_end, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchTousChamps",$vs_request_tous_champs, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchIdno",$vs_request_idno, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchLocalisation",$vs_request_localisation, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchDatation",$vs_request_datation, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchTechnique",$vs_request_technique, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchMateriau",$vs_request_materiau, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchTitre",$vs_request_titre, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchAuteur",$vs_request_auteur, time()+3600, __CA_URL_ROOT__);
			setcookie("simpleEditor".$this->ops_table_propername."SearchDomaine",$vs_request_domaine, time()+3600, __CA_URL_ROOT__);
			//die();

			$vs_num_results=12;

			// Default values
			$return = "";

			if(!(int) $vs_start) {$vs_start=1;}
			if(!(int) $vs_end) {$vs_end=$vs_num_results+1;}

			$vt_sl_search = new ObjectSearch();

			// Creating search request
			$vs_search_request = ($vs_request_tous_champs ? $vs_request_tous_champs : "");
			$vs_search_request .= ($vs_request_idno ? ($vs_search_request ? " AND " : "")."ca_objects.idno:".$vs_request_idno : "");
			$vs_search_request .= ($vs_request_localisation ? ($vs_search_request ? " AND " : "")."ca_storage_locations.preferred_labels.name:".$vs_request_localisation : "");
			$vs_search_request .= ($vs_request_datation ? ($vs_search_request ? " AND " : "")."ca_objects.objectProductionDate:".$vs_request_datation : "");
			$vs_search_request .= ($vs_request_materiau ? ($vs_search_request ? " AND " : "")."ca_objects.materiaux_tech_c.materiaux:".$vs_request_materiau : "");
			$vs_search_request .= ($vs_request_technique ? ($vs_search_request ? " AND " : "")."ca_objects.materiaux_tech_c.techniques:".$vs_request_technique : "");
			$vs_search_request .= ($vs_request_titre ? ($vs_search_request ? " AND " : "")."ca_objects.preferred_labels.name:".$vs_request_titre : "");
            $vs_search_request .= ($vs_request_auteur ? ($vs_search_request ? " AND " : "")."ca_entities.preferred_labels.displayname:".$vs_request_auteur : "");
			$vs_search_request .= ($vs_request_domaine ? ($vs_search_request ? " AND " : "")."ca_objects.type_id:".$vs_request_domaine : "");
			?>
			<script type="text/javascript">
				console.log("<?php print $vs_search_request; ?>");
			</script>
			<?php
			if(!$vs_search_request)  {$vs_search_request = "*";}
			$va_search_options = array("sort"=>"ca_objects.idno");
			$qr_results = $vt_sl_search->search($vs_search_request, $va_search_options);

			$count = 1;

			$vn_total_results = $qr_results->numHits();
?>
			<script type="text/javascript">
				console.log("total : <?php print $vn_total_results; ?>");
				console.log("end :<?php print $vs_end; ?>");
			</script>
<?php		if($vn_total_results==0) {
				$return .= "<p>Aucun résultat</p>";
			}

			while($qr_results->nextHit()) {
				if($count>= $vs_start && $count<= $vs_end) {
					if ($vs_get_spec = $this->getRequest()->config->get("ca_objects_lookup_settings")) {
						//var_dump($vs_get_spec);
						$row_class = "leftSearchResult leftSearchResult-".$count;
						$return .= "<div class=\"$row_class\" data-id=\"".$count."\" data-object_id=\"".$qr_results->get("ca_objects.object_id")."\" data-type_id=\"".$qr_results->get("ca_objects.type_id")."\"><a>";
						$return .= $qr_results->get("ca_object_representations.media.icon");
						$return .= $qr_results->get("ca_objects.preferred_labels");
						$return .= " <small>(".$qr_results->get("ca_objects.idno").")</small> ";
						$return .= "</a></div>";
					}
				}
				$count++;
				//var_dump();
				?>
<?php		
			}
			if ($vn_total_results > $vs_end) {
				$return .= "<a class=\"jscroll-next\" href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Objects/DoSearch/?search-start=".($vs_end+1)
                    ."&search-end=".($vs_end+$vs_num_results)
                    ."&pos="
                    ."&search-tous-champs=".htmlentities($vs_request_tous_champs)
                    ."&search-localisation=".htmlentities($vs_request_localisation)
                    ."&search-datation=".htmlentities($vs_request_datation)
                    ."&search-materiau=".htmlentities($vs_request_materiau)
                    ."&search-technique=".htmlentities($vs_request_technique)
                    ."&search-titre=".htmlentities($vs_request_titre)
                    ."&search-auteur=".htmlentities($vs_request_auteur)
                    ."&search-domaine=".htmlentities($vs_request_domaine)
                    ."&search-idno=".htmlentities($vs_request_idno)
                    ."\">next results</a>";
			} else {
				//$return .= "<a class=\"jscroll-next\"><!-- no more results to display, so no scrolling --></a>";
                $return .= "<a class=\"jscroll-next\" href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Objects/DoSearch/?search-start=".($vs_end+1)
                    ."&search-end=".($vs_end+$vs_num_results)
                    ."&pos="
                    ."&search-tous-champs=".htmlentities($vs_request_tous_champs)
                    ."&search-localisation=".htmlentities($vs_request_localisation)
                    ."&search-datation=".htmlentities($vs_request_datation)
                    ."&search-materiau=".htmlentities($vs_request_materiau)
                    ."&search-technique=".htmlentities($vs_request_technique)
                    ."&search-titre=".htmlentities($vs_request_titre)
                    ."&search-auteur=".htmlentities($vs_request_auteur)
                    ."&search-domaine=".htmlentities($vs_request_domaine)
                    ."&search-idno=".htmlentities($vs_request_idno)
                    ."\">next results</a>";
			}

			$return .= "
			<script>
			jQuery(document).ready(function() {
				jQuery(\".leftSearchResult\").on(\"click\", function(event) {
					event.preventDefault();
					console.log(jQuery(this).data(\"object_id\"));
					jQuery(this).css(\"background-color\",\"#ededed\");

					// similar behavior as clicking on a link
					var href = \"".__CA_URL_ROOT__."/index.php/simpleEditor/Objects/Edit/object_id/\"+jQuery(this).data(\"object_id\")+\"/type_id/\"+jQuery(this).data(\"type_id\")+\"/pos/\"+jQuery(this).data(\"id\")
					console.log(href);
					window.location.href = href;
					//jQuery('#leftSearchResult-form-pos').val(jQuery(this).data('id'));
					//jQuery('#leftSearchResult-form').submit();
				});
				
				jQuery(\".leftSearchResult-".$vn_pos."\").css(\"background-color\",\"#ededed\");

				/* hiding first results before the one clicked on */
				jQuery('#leftSearchResultShowBefore').on('click',function() {
					jQuery(this).hide();
					jQuery(\".leftSearchResult.hidden\").fadeIn().removeClass('hidden');
				});
			});
			</script>
			";

			print $return;
			exit();
		}


		public function SearchWidget() {
			$vn_pos = $this->request->getParameter('pos', pInteger);
			$this->view->setVar('pos', $vn_pos);
			$vb_showallresults = $this->request->getParameter('showallresults', pInteger);
			$this->view->setVar('showallresults', $vb_showallresults);
			
			return $this->render("search_widget_".strtolower($this->ops_table_propername)."_html.php",true);
		}

 	}
 ?>