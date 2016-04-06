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
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseAjaxController.php");

	class ObjectsController extends SimpleEditorBaseController {
 		# -------------------------------------------------------
  		protected $opo_config;		// plugin configuration file
		protected $ops_table_name = 'ca_objects';		// name of "subject" table (what we're editing)
		# -------------------------------------------------------
		public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
			parent::__construct($po_request, $po_response, $pa_view_paths);
			$this->ops_table_name="ca_objects";
			AssetLoadManager::register('panel');

 			if (!$this->request->user->canDoAction('can_use_simple_editor_plugin')) {
 				$this->response->setRedirect($this->request->config->get('error_display_url').'/n/3000?r='.urlencode($this->request->getFullUrlPath()));
 				return;
 			}
 			
 			$this->opo_config = Configuration::load(__CA_APP_DIR__.'/plugins/simpleEditor/conf/simpleEditor.conf');
			
 		}

 		# -------------------------------------------------------
 		# Functions to render views
 		# -------------------------------------------------------
 		public function Index($type="") {
		    $url = "/".str_ireplace("/index", "/Edit", $this->getRequest()->getRequestUrl());
		    $this->redirect($url);
 		}
 				
 		public function Edit($pa_values=null, $pa_options=null) {
		    $vn_object_id=$this->request->getParameter('object_id', pInteger);
			$url="";
		    if($vn_object_id) {
			    // Setting cookie of last edited object
			    $vt_item = new ca_objects();
				$vs_load_result = $vt_item->load($vn_object_id);
			    if(!$vs_load_result) {
				    //die("errors loading object");
				    $o_data = new Db();
				    $qr_result = $o_data->query("
					    SELECT max(object_id) as object_id
					    FROM ca_objects 
					    WHERE deleted = 0
					 ");

				    while($qr_result->nextRow()) {
					    $url = "/".str_ireplace("//","", str_ireplace("/object_id/$vn_object_id","",$this->getRequest()->getRequestUrl()))."/object_id/".$qr_result->get('object_id');
				    }
			    }
			    setcookie("simpleEditorObjectsControllerLastEdited", $vn_object_id, time()+3600*24*30,"/");

		    } elseif ((int) $_COOKIE["simpleEditorObjectsControllerLastEdited"]>0) {
			    $vn_object_id = $_COOKIE["simpleEditorObjectsControllerLastEdited"];
			    $url = "/".str_ireplace("//","", str_ireplace("/object_id/","",$this->getRequest()->getRequestUrl()))."/object_id/".$vn_object_id;
			    $this->redirect($url);
		    } else {
			    // Redirect to last added object
			    $o_data = new Db();
			    $qr_result = $o_data->query("
					    SELECT max(object_id) as object_id
					    FROM ca_objects 
					    WHERE deleted = 0
					 ");

			    while($qr_result->nextRow()) {
				    $url = "/".str_ireplace("//","", str_ireplace("/object_id/$vn_object_id","",$this->getRequest()->getRequestUrl()))."/object_id/".$qr_result->get('object_id');
			    }

		    }
		    if($url) {
			    var_dump($url);
			    print "<script>
			jQuery(document).ready(function() {
					// similar behavior as clicking on a link
					var href = \"".$url."\";
					window.location.href = href;
			});</script>"

			    ;
			    //die();
		    }
		    $vn_type_id=$this->request->getParameter('type_id', pInteger);

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

			// taken from BaseQuickAddController, there should be another to get default screen for an object, but it's 3 am...
			$t_ui = ca_editor_uis::loadDefaultUI("ca_objects", $this->request);
			$va_nav = $t_ui->getScreensAsNavConfigFragment($this->request, caGetDefaultItemID("object_types"), $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(),
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

		    if ($vn_object_id) {
				$this->view->setVar('object_id', $vn_object_id);
				$this->view->setVar('t_item', $vt_item);
				//$vt_representations = $vt_item->getRepresentations(array('preview170','medium'));
				//var_dump($vt_representations);
				//$this->view->setVar('representations', $vt_representations);
				parent::Edit($pa_values, $pa_options);
			} else {    
				parent::Edit($pa_values, $pa_options);
				//$this->render('edit_objects_html.php');
			}

 		}

		public function Add($pa_options = null) {
			$vn_object_id=$this->request->getParameter('object_id', pInteger);
			if($vn_object_id) {
				$url = "/".str_ireplace("/Add/", "/Edit/", $this->getRequest()->getRequestUrl()."/object_id/".$vn_object_id);
				$this->redirect($url);
			} else {
				$vn_type_id=$this->request->getParameter('type_id', pInteger);
				if(!$vn_type_id) {
					$vt_list_entity_types = new ca_lists();
					$vn_type_id = $vt_list_entity_types->getDefaultItemID("object_types");
				}
				$vt_object = new ca_objects();
				$vt_object->setMode(ACCESS_WRITE);
				//$pn_locale_id='1'; //Set the locale
				$vt_object->set(array('access' => 2, 'status' => 3, 'idno' => '','type_id' => $vn_type_id));//Define some intrinsic data.
				$vt_object->insert();//Insert the object
				if($vt_object->numErrors()) {
					var_dump($vt_object->getErrors());
					die("Oups. Erreur.");
				}
				$url = "/".str_ireplace("/Add/", "/Edit/", $this->getRequest()->getRequestUrl()."/object_id/".$vt_object->getPrimaryKey());
				$this->redirect($url);
			}
		}

		public function DoSearch() {

			AssetLoadManager::register('bundleableEditor');

			$vn_object_id = $this->request->getParameter('object_id', pInteger);

			$vn_pos = $this->request->getParameter('pos', pString);
			$vb_show_all_results = $this->request->getParameter('showallresults', pString);

			$vs_start = $this->request->getParameter('start', pInteger);
			$vs_end = $this->request->getParameter('end', pInteger);
			$vs_request_tous_champs = $this->request->getParameter('search-tous-champs', pString);
			$vs_request_idno = $this->request->getParameter('search-idno', pString);
			$vs_request_localisation = $this->request->getParameter('search-localisation', pString);
			$vs_request_datation = $this->request->getParameter('search-datation', pString);
			$vs_request_technique = $this->request->getParameter('search-technique', pString);
			$vs_request_titre = $this->request->getParameter('search-titre', pString);
			$vs_request_auteur = $this->request->getParameter('search-auteur', pString);
			$vs_request_domaine = $this->request->getParameter('search-domaine', pString);

			// Storing search form values inside a cookie
			setcookie("simpleEditorObjectsSearchPos",$vs_pos, 0, "/");
			setcookie("simpleEditorObjectsSearchStart",$vs_start, 0, "/");
			setcookie("simpleEditorObjectsSearchEnd",$vs_end, 0, "/");
			setcookie("simpleEditorObjectsSearchTousChamps",$vs_request_tous_champs, 0, "/");
			setcookie("simpleEditorObjectsSearchIdno",$vs_request_idno, 0, "/");
			setcookie("simpleEditorObjectsSearchLocalisation",$vs_request_localisation, 0, "/");
			setcookie("simpleEditorObjectsSearchDatation",$vs_request_datation, 0, "/");
			setcookie("simpleEditorObjectsSearchTechnique",$vs_request_technique, 0, "/");
			setcookie("simpleEditorObjectsSearchTitre",$vs_request_titre, 0, "/");
			setcookie("simpleEditorObjectsSearchAuteur",$vs_request_auteur, 0, "/");
			setcookie("simpleEditorObjectsSearchDomaine",$vs_request_domaine, 0, "/");

			$vs_num_results=12;

			// Default values
			$return = "";

			if(!(int) $vs_start) {$vs_start=1;}
			if(((int)$vn_pos >0 || $vn_pos === "0") && ($vb_show_all_results != 1)) {
				//$vs_start = $vn_pos;
				$vs_end=$vn_pos + $vs_num_results;
				$return .= "<div><a id=\"leftSearchResultShowBefore\">...</div>";
			}
			if(!(int) $vs_end) {$vs_end=$vs_num_results;}

			$vt_sl_search = new ObjectSearch();

			// Creating search request
			$vs_search_request = ($vs_request_tous_champs ? $vs_request_tous_champs : "");
			$vs_search_request .= ($vs_request_idno ? ($vs_search_request ? " AND " : "")."ca_objects.idno:".$vs_request_idno : "");
			$vs_search_request .= ($vs_request_localisation ? ($vs_search_request ? " AND " : "")."ca_storage_locations.preferred_labels.name:".$vs_request_localisation : "");
			$vs_search_request .= ($vs_request_datation ? ($vs_search_request ? " AND " : "")."ca_objects.objectProductionDate:".$vs_request_datation : "");
			$vs_search_request .= ($vs_request_technique ? ($vs_search_request ? " AND " : "")."ca_objects.idno:".$vs_request_technique : "");
			$vs_search_request .= ($vs_request_titre ? ($vs_search_request ? " AND " : "")."ca_objects.preferred_labels.name:".$vs_request_titre : "");
            $vs_search_request .= ($vs_request_auteur ? ($vs_search_request ? " AND " : "")."ca_entities.preferred_labels.displayname:".$vs_request_auteur : "");
			$vs_search_request .= ($vs_request_domaine ? ($vs_search_request ? " AND " : "")."ca_objects.domaine:".$vs_request_domaine : "");
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


			while($qr_results->nextHit()) {
				if($count>= $vs_start && $count<= $vs_end) {
					if ($vs_get_spec = $this->getRequest()->config->get("ca_objects_lookup_settings")) {
						//var_dump($vs_get_spec);
						if($count>=$vn_pos) {
							$row_class = "leftSearchResult leftSearchResult-".$count;
						} else {
							$row_class = "leftSearchResult leftSearchResult-".$count." hidden";
						}
						$return .= "<div class=\"$row_class\" data-id=\"".$count."\" data-object_id=\"".$qr_results->get("ca_objects.object_id")."\"><a>";
						$return .= $qr_results->get("ca_object_representations.media.icon");
						$return .= $qr_results->get("ca_objects.preferred_labels");
						$return .= " <small>(".$qr_results->get("ca_objects.idno").")</small> ";
						$return .= "</a></div>";
					}
				}
				//var_dump();
				$count++;
			}
			if ($vn_total_results > $vs_end) {
				$return .= "<a class=\"jscroll-next\" href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Objects/DoSearch/?start=".($vs_end+1)."&end=".($vs_end+$vs_num_results)."\">next results</a>";
			}

			$return .= "
			<script>
			jQuery(document).ready(function() {
				jQuery(\".leftSearchResult\").on(\"click\", function(event) {
					event.preventDefault();
					console.log(jQuery(this).data(\"object_id\"));
					jQuery(this).css(\"background-color\",\"#ededed\");

					// similar behavior as clicking on a link
					var href = \"".__CA_URL_ROOT__."/index.php/simpleEditor/Objects/Edit/object_id/\"+jQuery(this).data(\"object_id\")+\"/pos/\"+jQuery(this).data(\"id\")
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
			return $this->render("search_widget_objects_html.php",true);
		}
 	}
 ?>