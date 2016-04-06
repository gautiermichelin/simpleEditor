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
require_once(__CA_MODELS_DIR__.'/ca_entities.php');
require_once(__CA_MODELS_DIR__.'/ca_object_representations_x_entities.php');
require_once(__CA_MODELS_DIR__.'/ca_locales.php');


require_once(__CA_LIB_DIR__."/ca/Search/EntitySearch.php");
require_once(__CA_LIB_DIR__."/ca/Search/EntitySearchResult.php");

require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseController.php");
require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseAjaxController.php");

class EntitiesController extends SimpleEditorBaseController {
	# -------------------------------------------------------
	protected $opo_config;		// plugin configuration file
	protected $ops_table_name = 'ca_entities';		// name of "subject" table (what we're editing)
	# -------------------------------------------------------
	public function __construct(&$po_request, &$po_response, $pa_view_paths=null) {
		parent::__construct($po_request, $po_response, $pa_view_paths);
		$this->ops_table_name="ca_entities";
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
		$vn_subject_id=$this->request->getParameter('entity_id', pInteger);
		if($vn_subject_id) {
			// Setting cookie of last edited object
			setcookie("simpleEditorEntitiesControllerLastEdited", $vn_subject_id, time()+3600*24*30,"/");
		} elseif ((int) $_COOKIE["simpleEditorEntitiesControllerLastEdited"]>0) {
			$vn_subject_id = $_COOKIE["simpleEditorEntitiesControllerLastEdited"];
			$url = "/".str_ireplace("//","", str_ireplace("/entity_id/","",$this->getRequest()->getRequestUrl()))."/entity_id/".$vn_subject_id;
			$this->redirect($url);
		} else {
			// Redirect to last added object
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
		$t_ui = ca_editor_uis::loadDefaultUI("ca_entities", $this->request);
		$va_nav = $t_ui->getScreensAsNavConfigFragment($this->request, caGetDefaultItemID("entity_types"), $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(),
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
		//var_dump($vs_last_selected_path_item);die();
		$this->view->setVar('last_selected_path_item', $vs_last_selected_path_item);

		if ($vn_type_id) {
			$this->view->setVar('type_id', $vn_type_id);
		}

		if ($vn_subject_id) {
			$this->view->setVar('subject_id', $vn_subject_id);
			$vt_item = new ca_entities($vn_subject_id);
			$this->view->setVar('t_item', $vt_item);
			$vt_representations = $vt_item->getRepresentations(array('preview170','medium'));
			//var_dump($vt_representations);
			$this->view->setVar('representations', $vt_representations);
			parent::Edit($pa_values, $pa_options);
		} else {
			parent::Edit($pa_values, $pa_options);
			//$this->render('edit_objects_html.php');
		}

	}

	public function Save($pa_options=null) {
		$vn_subject_id=$this->request->getParameter('entity_id', pInteger);
		if($vn_subject_id) {
			// Setting cookie of last edited object
			setcookie("simpleEditorEntitiesControllerLastEdited", $vn_subject_id, time()+3600*24*30,"/");
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

		// taken from BaseQuickAddController, there should be another to get default screen for an object, but it's 3 am...
		$t_ui = ca_editor_uis::loadDefaultUI("ca_entities", $this->request);
		$va_nav = $t_ui->getScreensAsNavConfigFragment($this->request, caGetDefaultItemID("entity_types"), $this->request->getModulePath(), $this->request->getController(), $this->request->getAction(),
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

		if ($vn_type_id) {
			$this->view->setVar('type_id', $vn_type_id);
		}

		if ($vn_subject_id) {
			$this->view->setVar('entity_id', $vn_subject_id);
			$vt_item = new ca_entities($vn_subject_id);
			$this->view->setVar('t_item', $vt_item);
			$vt_representations = $vt_item->getRepresentations(array('preview170','medium'));
			$this->view->setVar('representations', $vt_representations);
			parent::Save($pa_options);
		} else {
			parent::Save($pa_options);
		}

	}

	public function Add($pa_options = null) {
		$vn_subject_id=$this->request->getParameter('entity_id', pInteger);
		if($vn_subject_id) {
			$url = "/".str_ireplace("/Add/", "/Edit/", $this->getRequest()->getRequestUrl()."/entity_id/".$vn_subject_id);
			$this->redirect($url);
		} else {
			$vn_type_id=$this->request->getParameter('type_id', pInteger);
			if(!$vn_type_id) {
				//$vt_list_entity_types = new ca_lists();
				//$vn_type_id = $vt_list_entity_types->getDefaultItemID("entity_types");
				$vn_type_id = caGetDefaultItemID("entity_types");
			}
			$vt_item = new ca_entities();
			$vt_item->setMode(ACCESS_WRITE);
			//Define some intrinsic data.
			$vt_item->set(array('access' => 2, 'status' => 3, 'idno' => '','type_id' => $vn_type_id));
			//Insert the object
			$vt_item->insert();
			if($vt_item->numErrors()) {
				var_dump($vt_item->getErrors());
				die("Oups. Erreur.");
			}
			$url = "/".str_ireplace("/Add/", "/Edit/", $this->getRequest()->getRequestUrl()."/entity_id/".$vt_item->getPrimaryKey());
			$this->redirect($url);
		}
	}

	public function DoSearch() {

		$vs_start = $this->request->getParameter('start', pInteger);
		$vs_end = $this->request->getParameter('end', pInteger);
		$vs_request_idno = $this->request->getParameter('search-idno', pString);
		$vs_request_tous_champs = $this->request->getParameter('search-tous-champs', pString);
		$vs_request_localisation = $this->request->getParameter('search-localisation', pString);
		$vs_request_datation = $this->request->getParameter('search-datation', pString);
		$vs_request_technique = $this->request->getParameter('search-technique', pString);
		$vs_request_titre = $this->request->getParameter('search-titre', pString);
		$vs_request_auteur = $this->request->getParameter('search-auteur', pString);
		$vs_request_domaine = $this->request->getParameter('search-domaine', pString);

		setcookie("simpleEditorEntitiesIdno",$vs_request_idno);
		setcookie("simpleEditorEntitiesTousChamps",$vs_request_tous_champs);
		setcookie("simpleEditorEntitiesLocalisation",$vs_request_localisation);
		setcookie("simpleEditorEntitiesDatation",$vs_request_datation);
		setcookie("simpleEditorEntitiesTechnique",$vs_request_technique);
		setcookie("simpleEditorEntitiesTitre",$vs_request_titre);
		setcookie("simpleEditorEntitiesAuteur",$vs_request_auteur);
		setcookie("simpleEditorEntitiesDomaine",$vs_request_domaine);

		$vs_num_results=12;

		// Default values
		if(!(int) $vs_start) {$vs_start=1;}
		if(!(int) $vs_end) {$vs_end=$vs_num_results;}
		if(!$vs_request_idno)  {$vs_request_idno = "*";}
		//var_dump("ca_objects.idno:\"".$vs_request_idno."\"");
		//die();
		//$return =array("start"=>$vs_start, "end"=>$vs_end, "request"=>$vs_request);

		$vt_sl_search = new EntitySearch();
		$vs_search_request = ($vs_request_tous_champs ? $vs_request_tous_champs : "");
		$vs_search_request .= ($vs_request_idno ? "ca_entities.idno:".$vs_request_idno : "");
		$vs_search_request .= ($vs_request_localisation ? ($vs_search_request ? " AND " : "")."ca_storage_locations.preferred_labels.name:\"".$vs_request_localisation."\"" : "");
		$vs_search_request .= ($vs_request_titre ? ($vs_search_request ? " AND " : "")."ca_entities.preferred_labels.displayname:\"".$vs_request_titre."\"" : ""); /**/

		$qr_results = $vt_sl_search->search($vs_search_request);

		$count = 1;
		$vn_total_results = $qr_results->numHits();

		while($qr_results->nextHit()) {
			if($count>= $vs_start && $count<= $vs_end) {
				if ($vs_get_spec = $this->getRequest()->config->get("ca_entities_lookup_settings")) {
					//var_dump($vs_get_spec);
					$return .= "<div class=\"leftSearchResult\"><a href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Entities/Edit/entity_id/".$qr_results->get("ca_entities.entity_id")."\">";
					$return .= $qr_results->get("ca_object_representations.media.icon");
					$return .= $qr_results->get("ca_entities.preferred_labels.displayname");
					$return .= " <small>(".$qr_results->get("ca_entities.idno").")</small> ";
					$return .= "</a></div>";

				}
			}
			$count++;
		}
		if ($vn_total_results > $vs_end) {
			$return .= "<a class=\"jscroll-next\" href=\"".__CA_URL_ROOT__."/index.php/simpleEditor/Entities/DoSearch/?start=".($vs_end+1)."&end=".($vs_end+$vs_num_results)."\">next results</a>";
		}

		print $return;
		exit();
	}

	public function SearchWidget() {
		return $this->render("search_widget_entities_html.php",true);
	}
}
