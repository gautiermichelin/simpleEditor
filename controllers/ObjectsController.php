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
 			$universe=$this->request->getParameter('universe', pString);
 			if(!isset($universe)) {
 				_p("No corresponding table (or stat universe) declared.");
 			} else {
				$this->view->setVar('statistics_listing', $this->opa_statistics[$universe]);
				$this->render('stats_home_html.php');			 				
 			}
 		}
 				
 		public function Edit($pa_values=null, $pa_options=null) {
			$vn_object_id=$this->request->getParameter('object_id', pInteger);

			AssetLoadManager::register('bundleableEditor');
			AssetLoadManager::register('imageScroller');
			AssetLoadManager::register('ckeditor');

			//var_dump($vn_object_id);die();
			// Loading specific CSS
			MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins/simpleEditor/assets/css/simpleEditor.css",'text/css');

			// storing current screen number
			$vs_last_selected_path_item = $this->getRequest()->getActionExtra();
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
			// Getting all screens
			$va_screens = $va_nav["fragment"];
			// Keeping here only non default screen
			unset($va_screens[str_replace("Screen","screen_",$va_nav['defaultScreen'])]);
			$this->view->setVar('screens', $va_screens);

			// If we don't have a default screen loaded, avoid loading the default one, as it is already on the top left box
			if(!$vs_last_selected_path_item || ($vs_last_selected_path_item=="Edit/".$va_nav['defaultScreen'])) {
				$vs_last_selected_path_item = reset($va_screens)["default"]["action"];
			};
			$this->view->setVar('last_selected_path_item', $vs_last_selected_path_item);

			if ($vn_object_id) {
				$this->view->setVar('object_id', $vn_object_id);
				$vt_item = new ca_objects($vn_object_id);
				$this->view->setVar('t_item', $vt_item);
				$vt_representations = $vt_item->getRepresentations();
				//var_dump($vt_representations);
				$this->view->setVar('representations', $vt_representations);
				return parent::Edit($pa_values, $pa_options);
			} else {
				$this->render('edit_objects_html.php');
			}

 		}

		public function Save($pa_options = null) {
			//return parent::Save($pa_options); // TODO: Change the autogenerated stub
			die("ici");
		}

		public function DoSearch() {

			//$_POST=array();
			//var_dump($_POST);die();

			$vs_start = $this->request->getParameter('start', pInteger);
			$vs_end = $this->request->getParameter('end', pInteger);
			$vs_request_idno = $this->request->getParameter('search-idno', pString);
			$vs_request_localisation = $this->request->getParameter('search-localisation', pString);
			$vs_request_datation = $this->request->getParameter('search-datation', pString);
			$vs_request_technique = $this->request->getParameter('search-technique', pString);
			$vs_request_titre = $this->request->getParameter('search-titre', pString);
			$vs_request_auteur = $this->request->getParameter('search-auteur', pString);
			$vs_request_domaine = $this->request->getParameter('search-domaine', pString);

			setcookie("simpleEditorObjectsIdno",$vs_request_idno);
			setcookie("simpleEditorObjectsLocalisation",$vs_request_localisation);
			setcookie("simpleEditorObjectsDatation",$vs_request_datation);
			setcookie("simpleEditorObjectsTechnique",$vs_request_technique);
			setcookie("simpleEditorObjectsTitre",$vs_request_titre);
			setcookie("simpleEditorObjectsAuteur",$vs_request_auteur);
			setcookie("simpleEditorObjectsDomaine",$vs_request_domaine);

			$vs_num_results=12;

			// Default values
			if(!(int) $vs_start) {$vs_start=1;}
			if(!(int) $vs_end) {$vs_end=$vs_num_results;}
			if(!$vs_request_idno)  {$vs_request_idno = "*";}
			//var_dump("ca_objects.idno:\"".$vs_request_idno."\"");
			//die();
			//$return =array("start"=>$vs_start, "end"=>$vs_end, "request"=>$vs_request);

			$vt_sl_search = new ObjectSearch();
			$vs_search_request .= ($vs_request_idno ? "ca_objects.idno:".$vs_request_idno : "");


			$vs_search_request .= ($vs_request_localisation ? ($vs_search_request ? " AND " : "")."ca_storage_locations.preferred_labels.name:\"".$vs_request_localisation."\"" : "");
			/*$vs_search_request .= ($vs_request_datation ? "ca_objects.idno:".$vs_request_datation : "");
			$vs_search_request .= ($vs_request_technique ? "ca_objects.idno:".$vs_request_technique : "");*/
			$vs_search_request .= ($vs_request_titre ? ($vs_search_request ? " AND " : "")."ca_objects.preferred_labels.name:\"".$vs_request_titre."\"" : ""); /*
			$vs_search_request .= ($vs_request_auteur ? "ca_objects.idno:".$vs_request_auteur : "").
			$vs_search_request .= ($vs_request_domaine ? "ca_objects.idno:".$vs_request_domaine : "");*/
			//var_dump($vs_search_request);
			//die();

			$qr_results = $vt_sl_search->search($vs_search_request);

			$count = 1;
			//$return["results"] = $qr_results->numHits();
			$vn_total_results = $qr_results->numHits();

			while($qr_results->nextHit()) {
				if($count>= $vs_start && $count<= $vs_end) {
					if ($vs_get_spec = $this->getRequest()->config->get("ca_objects_lookup_settings")) {
						//var_dump($vs_get_spec);
						$return .= "<div class=\"leftSearchResult\"><a href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Objects/Edit/object_id/".$qr_results->get("ca_objects.object_id")."\">";
						$return .= $qr_results->get("ca_object_representations.media.icon");
						$return .= "(".$qr_results->get("ca_objects.idno").") ";
						$return .= $qr_results->get("ca_objects.preferred_labels");
						$return .= "</a></div>";
						//$vs_label = caProcessTemplateForIDs($vs_get_spec, "ca_objects", array($qr_results->get('ca_objects.object_id')));

					}
					//$return["html"] .= $vs_label;
				}
				//var_dump();
				$count++;
			}
			if ($vn_total_results > $vs_end) {
				$return .= "<a class=\"jscroll-next\" href=\"http://villamedicis3.dev/index.php/simpleEditor/Objects/DoSearch/?start=".($vs_end+1)."&end=".($vs_end+$vs_num_results)."&request=".htmlentities($vs_request_idno)."\">next results</a>";
			}

			//$return["reponse"] = 'ok';

			//echo json_encode($return);
			print $return;
			exit();
		}

		public function SearchWidget() {
			return $this->render("search_widget_objects_html.php",true);
		}
 	}
 ?>