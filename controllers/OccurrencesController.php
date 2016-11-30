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
	require_once(__CA_MODELS_DIR__.'/ca_occurrences.php');
	require_once(__CA_MODELS_DIR__.'/ca_occurrence_labels.php');
 	require_once(__CA_MODELS_DIR__.'/ca_locales.php');


	require_once(__CA_LIB_DIR__."/ca/Search/OccurrenceSearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/OccurrenceSearchResult.php");

	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseController.php");
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorController.php");
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseAjaxController.php");

	class OccurrencesController extends SimpleEditorController {
		protected $ops_search_cookie_prefix = 'simpleEditorOccurrencesControllerSearch_';		// cookie prefix eventually followed by record type

		# -------------------------------------------------------
		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			parent::__construct($po_request, $po_response, $pa_view_paths);

			AssetLoadManager::register('panel');

 			if (!$this->request->user->canDoAction('can_use_simple_editor_plugin')) {
 				$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
 				return;
 			}

 			//$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/simpleEditor/conf/simpleEditor.conf');
 			$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/simpleEditor/conf/simpleEditor.conf');
			$this->ops_table_name = 'ca_occurrences';		// name of "subject" table (what we're editing)
			$this->ops_cookie_prefix = 'simpleEditorOccurrenceControllerLastEdited_';		// cookie prefix eventually followed by record type
			
			$this->ops_record_id = 'occurrence_id';		// name of "subject" table (what we're editing)
			$this->ops_table_propername = "Occurrences";
			$this->ops_table_type_list = 'occurrence_types';
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
			$vn_type_id = $this->ops_type_id;

			$vb_show_all_results = $this->request->getParameter('showallresults', pString);

			$vs_start = $this->request->getParameter('start', pInteger);
			$vs_end = $this->request->getParameter('end', pInteger);
			$vs_request_tous_champs = $this->request->getParameter('search-tous-champs', pString);
			$vs_request_titre = $this->request->getParameter('search-titre', pString);

			$ops_cookie_prefix=$this->ops_search_cookie_prefix;
			// Storing search form values inside a cookie
			setcookie($ops_cookie_prefix."_".$vn_type_id."_Pos",$vs_pos, 0, "/");
			setcookie($ops_cookie_prefix."_".$vn_type_id."_Start",$vs_start, 0, "/");
			setcookie($ops_cookie_prefix."_".$vn_type_id."_End",$vs_end, 0, "/");
			setcookie($ops_cookie_prefix."_".$vn_type_id."_TousChamps",$vs_request_tous_champs, 0, "/");
			setcookie($ops_cookie_prefix."_".$vn_type_id."_Titre",$vs_request_titre, 0, "/");

			$vs_num_results=12;

			// Default values
			$return = "";

			if(!(int) $vs_start) {$vs_start=1;}
			if(!(int) $vs_end) {$vs_end=$vs_num_results;}

			$vt_sl_search = new OccurrenceSearch();

			// Creating search request
			$vs_search_request = ($vs_request_tous_champs ? $vs_request_tous_champs : "");
			$vs_search_request .= ($vs_request_titre ? ($vs_search_request ? " AND " : "")."ca_occurrences.preferred_labels:".$vs_request_titre : "");
			$vs_search_request .= ($vn_type_id ? ($vs_search_request ? " AND " : "")."ca_occurrences.type_id:\"".$vn_type_id."\"": "");
			
			?>
			<script type="text/javascript">
				console.log('<?php print $vs_search_request; ?>');
			</script>
			<?php
			if(!$vs_search_request)  {$vs_search_request = "*";}
			$va_search_options = array("sort"=>"ca_occurrences.preferred_labels");
			$qr_results = $vt_sl_search->search($vs_search_request, $va_search_options);

			$count = 1;

			$vn_total_results = $qr_results->numHits();
			print "<p>".$qr_results->numHits()." résultat(s)</p>";


			while($qr_results->nextHit()) {
				if($count>= $vs_start && $count<= $vs_end) {
					//var_dump($vs_get_spec);
					$row_class = "leftSearchResult leftSearchResult-occurrences leftSearchResult-".$count;
					$return .= "<div class=\"$row_class\" data-id=\"".$count."\" data-type_id=\"".$qr_results->get("ca_occurrences.type_id")."\" data-occurrence_id=\"".$qr_results->get("ca_occurrences.occurrence_id")."\"><a>";
					$return .= ($qr_results->get("ca_occurrences.preferred_labels") ? : "[VIDE]");
					$return .= "</a></div>";
				}
				//var_dump();
				$count++;
			}
			if ($vn_total_results > $vs_end) {
				$return .= "<a class=\"jscroll-next\" href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Occurrences/DoSearch/?start=".($vs_end+1)."&end=".($vs_end+$vs_num_results)."\">next results</a>";
			} else {
				$return .= "<a class=\"jscroll-next\"><!-- no more results to display, so no scrolling --></a>";
			}

			$return .= "
			<script>
			jQuery(document).ready(function() {
				jQuery(\".leftSearchResult\").on(\"click\", function(event) {
					event.preventDefault();
					console.log(jQuery(this).data(\"occurrence_id\"));
					jQuery(this).css(\"background-color\",\"#ededed\");

					// similar behavior as clicking on a link
					var href = \"".__CA_URL_ROOT__."/index.php/simpleEditor/Occurrences/Edit/type_id/\"+jQuery(this).data(\"type_id\")+\"/occurrence_id/\"+jQuery(this).data(\"occurrence_id\")+\"/pos/\"+jQuery(this).data(\"id\")
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
			$vn_type_id = $this->ops_type_id;

			$this->view->setVar('pos', $vn_pos);
			$this->view->setVar('type_id', $vn_type_id);
			$this->view->setVar('cookie_prefix', $this->ops_search_cookie_prefix);

			$vb_showallresults = $this->request->getParameter('showallresults', pInteger);
			$this->view->setVar('showallresults', $vb_showallresults);
			return $this->render("search_widget_".strtolower($this->ops_table_propername)."_html.php",true);
		}

		/*public function Save($pa_values=null, $pa_options=null) {
			$vn_record_id=$this->request->getParameter($this->ops_record_id, pInteger);
			$vs_request_url = $this->getRequest()->getRequestUrl();
			$vn_type_id=$this->request->getParameter('type_id', pInteger);

			$url="";
			$cookieLastEditedIsValid=false;
			// check cookie validity
			if (((int) $_COOKIE["simpleEditor".$this->ops_table_propername."ControllerLastEdited"]>0) && (!$vn_record_id)) {
				$vn_lastedited_record_id = $_COOKIE["simpleEditor".$this->ops_table_propername."ControllerLastEdited"];
				$o_data = new Db();
				$vs_query = "SELECT ".$this->ops_record_id." FROM ".$this->ops_table_name." WHERE deleted = 0 AND ".$this->ops_record_id." = ?";
				var_dump($vs_query);die();
				$qr_result = $o_data->query($vs_query, $vn_lastedited_record_id);
				$cookieLastEditedIsValid = (sizeof($qr_result->getAllRows()) > 0);
			}
			//die($vn_record_id);
			if($vn_record_id) {
				// Setting cookie of last edited record
				$vs_class_name = $this->ops_table_name;
				$vt_item = new $vs_class_name();
				$vs_load_result = $vt_item->load($vn_record_id);
				setcookie("simpleEditor".$this->ops_table_propername."ControllerLastEdited", $vn_record_id, time()+3600*24*30,"/");
			} elseif (!$cookieLastEditedIsValid) {
				//die("cookie invalide");
				// Redirect to last added record
				$o_data = new Db();
				$qr_result = $o_data->query("
					    SELECT min(".$this->ops_record_id.") as ".$this->ops_record_id."
					    FROM ".$this->ops_table_name." 
					    WHERE deleted = 0
					 ");

				while($qr_result->nextRow()) {
					if ($qr_result->get($this->ops_record_id)) {
						$url = "/".str_ireplace("//","", str_ireplace("/".$this->ops_record_id."/$vn_record_id","",$vs_request_url))."/".$this->ops_record_id."/".$qr_result->get($this->ops_record_id);
						break;
					}
				}
			} else {
				//die("cookie valide");
				$url = "/".str_ireplace("//","", str_ireplace("/".$this->ops_record_id."/$vn_record_id","",$vs_request_url))."/".$this->ops_record_id."/".$vn_lastedited_record_id;
			}
			if($url) {
				//var_dump($url);
				print "<script>
        document.location.href=\"".$url."\";
			</script>";
				die();
			}


			AssetLoadManager::register('bundleableEditor');
			AssetLoadManager::register('imageScroller');
			AssetLoadManager::register('ckeditor');
			$script = file_get_contents(__CA_APP_DIR__."/plugins/simpleEditor/assets/js/simpleEditor.js");
			AssetLoadManager::addComplementaryScript($script);

			// Loading specific CSS
			MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins/simpleEditor/assets/css/simpleEditor.css",'text/css');

			// storing current screen number
			$vs_last_selected_path_item = $this->getRequest()->getActionExtra();

			// checking inside the URL if we need to enclose with a FORM tag
			$form = $this->getRequest()->getParameter("form",pString);
			$this->view->setVar('form_tag', $form);


			//var_dump($this->getRequest()->getActionExtra());
			//die();

			// taken from BaseQuickAddController, there should be another to get default screen for an record, but it's 3 am...
			$t_ui = ca_editor_uis::loadDefaultUI($this->ops_table_name, $this->request);
			$va_nav = $t_ui->getScreensAsNavConfigFragment($this->request, caGetDefaultItemID($this->ops_table_type_list), $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(),
				array(),
				array(),
				false,
				array('hideIfNoAccess' => isset($pa_params['hideIfNoAccess']) ? $pa_params['hideIfNoAccess'] : false, 'returnTypeRestrictions' => true)
			);
			// Defining default screen
			$this->view->setVar('default_screen', $va_nav['defaultScreen']);
			//var_dump($va_nav['defaultScreen']);die();
			// Getting all screens
			$va_screens = $va_nav["fragment"];
			// Keeping here only non default screen
			unset($va_screens[str_replace("Screen","screen_",$va_nav['defaultScreen'])]);
			$this->view->setVar('screens', $va_screens);
			// If we don't have a default screen loaded, avoid loading the default one, as it is already on the top left box
			if(!$vs_last_selected_path_item || ($vs_last_selected_path_item=="Edit/".$va_nav['defaultScreen'])) {
				$vs_last_selected_path_item = reset($va_screens)["default"]["action"];
			};
			//var_dump($vs_last_selected_path_item);die();
			$this->view->setVar('last_selected_path_item', $vs_last_selected_path_item);

			if ($vn_type_id) {
				$this->view->setVar('type_id', $vn_type_id);
			}
			if ($vn_record_id) {
				$this->view->setVar($this->ops_record_id, $vn_record_id);
				$this->view->setVar('t_item', $vt_item);
				$vt_representations = $vt_item->getRepresentations(array('preview170','medium'));
				//var_dump($vt_representations);
				//die();
				$this->view->setVar('representations', $vt_representations);
				parent::Save($pa_values, $pa_options);
			} else {
				//parent::Save($pa_values, $pa_options);
				die("Un identifiant est nécessaire pour l'enregistrement");
			}
		}*/
 	}
 ?>