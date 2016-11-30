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

	class EntitiesAjaxController extends SimpleEditorBaseAjaxController {
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

 		public function DownloadAttributeFile($pa_options=null) {
	 		ini_set("display_errors",1);
	 		error_reporting(E_ERROR);
			$url =$this->request->getFullUrlPath();
			$this->response->setRedirect(str_replace("simpleEditor/EntitiesAjax", "editor/entities/EntityEditor", $url));
			return;
		}

		public function EditAjax($pa_values=null, $pa_options=null) {
			$vn_subject_id=$this->request->getParameter('entity_id', pInteger);
			$vn_type_id=$this->request->getParameter("type_id", pInteger);

			AssetLoadManager::register('bundleableEditor');
			AssetLoadManager::register('imageScroller');
			AssetLoadManager::register('ckeditor');

			// Loading specific CSS
			MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins/simpleEditor/assets/css/simpleEditor.css",'text/css');

			// storing current screen number
			$vs_last_selected_path_item = $this->getRequest()->getActionExtra();

			// checking inside the URL if we need to enclose with a FORM tag
			$form = $this->getRequest()->getParameter("form",pString);
			$this->view->setVar('form', $form);

			if(!$vs_last_selected_path_item) {
				foreach(explode("/",$_SERVER['PHP_SELF']) as $url_part) {
					//Searching for screenname inside current URL
					if(substr_compare("Screen", $url_part, 0, 6) == 0){
						$vs_last_selected_path_item = "editAjax/".$url_part;
					}
				}
			}
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
			//unset($va_screens[str_replace("Screen","screen_",$va_nav['defaultScreen'])]);
			$this->view->setVar('screens', $va_screens);
			// If we don't have a default screen loaded, avoid loading the default one, as it is already on the top left box
			if(!$vs_last_selected_path_item || ($vs_last_selected_path_item=="editAjax/".$va_nav['defaultScreen'])) {
				$vs_last_selected_path_item = reset($va_screens)["default"]["action"];
			};
			//var_dump($vs_last_selected_path_item);die();
			$this->view->setVar('last_selected_path_item', $vs_last_selected_path_item);

			if ($vn_subject_id) {
				$this->view->setVar('subject_id', $vn_subject_id);
				$vt_item = new ca_entities($vn_subject_id);
				$this->view->setVar('t_item', $vt_item);
				$vt_representations = $vt_item->getRepresentations(array('preview170','medium'));
				$this->view->setVar('representations', $vt_representations);
				$this->view->setVar('type_id', $vn_type_id);
				print parent::Edit($pa_values, array("view"=>"screen_ajax_html"));
				exit();
			} else {
				print parent::Edit($pa_values, array("view"=>"screen_ajax_html"));
				exit();
			}
		}
 	}
