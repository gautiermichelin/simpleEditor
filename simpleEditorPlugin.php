<?php
/* ----------------------------------------------------------------------
 * mediaImportPlugin.php : 
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
  
	class simpleEditorPlugin extends BaseApplicationPlugin {
		# -------------------------------------------------------
		protected $description = 'Simple Editor for CollectiveAccess';
		# -------------------------------------------------------
		private $opo_config;
		private $ops_plugin_path;
		# -------------------------------------------------------
		public function __construct($ps_plugin_path) {
			$this->ops_plugin_path = $ps_plugin_path;
			$this->description = _t('A simplified UI for CollectiveAccess, build around a central database object editor as main window.');
			parent::__construct();
			$this->opo_config = Configuration::load($ps_plugin_path.'/conf/simpleEditor.conf');
		}
		# -------------------------------------------------------
		/**
		 * Override checkStatus() to return true - the statisticsViewerPlugin always initializes ok... (part to complete)
		 */
		public function checkStatus() {
			return array(
				'description' => $this->getDescription(),
				'errors' => array(),
				'warnings' => array(),
				'available' => ((bool)$this->opo_config->get('enabled'))
			);
		}
		# -------------------------------------------------------
		/**
		 * Insert activity menu
		 */
		public function hookRenderMenuBar($pa_menu_bar) {
			if ($o_req = $this->getRequest()) {
				//if (!$o_req->user->canDoAction('can_use_media_import_plugin')) { return true; }
				
				if (isset($pa_menu_bar['simpleEditor_menu'])) {
					$va_simple_editor_menu_edit_items = $pa_menu_bar['simpleEditor_menu_edit']['navigation'];
					if (!is_array($va_simple_editor_menu_edit_items)) { $va_simple_editor_menu_edit_items = array(); }
				} else {
					$va_simple_editor_menu_edit_items = array();
				}

				$va_simple_editor_menu_new_items = array();

				$va_simple_editor_menu_new_items["new_object_tirage"] = array(
					'displayName' => "Tirage",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/711'
					)
				);
				$va_simple_editor_menu_new_items["new_object_peinture"] = array(
					'displayName' => "Peinture",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14209'
					)
				);
				$va_simple_editor_menu_new_items["new_object_arts_graphiques"] = array(
					'displayName' => "Arts graphiques",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14210'
					)
				);
				$va_simple_editor_menu_new_items["new_object_mobilier"] = array(
					'displayName' => "Meuble",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14211'
					)
				);
				$va_simple_editor_menu_new_items["new_object_instruments"] = array(
					'displayName' => "Instrument de musique",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14407'
					)
				);
				$va_simple_editor_menu_new_items["new_object_sculptures"] = array(
					'displayName' => "Sculpture",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14212'
					)
				);				
				$va_simple_editor_menu_new_items["new_object_objet_art"] = array(
					'displayName' => "Objet d'art",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14213'
					)
				);
				$va_simple_editor_menu_new_items["new_object_tapisserie"] = array(
					'displayName' => "Tapisserie",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14214'
					)
				);
				$va_simple_editor_menu_new_items["new_object_photographies"] = array(
					'displayName' => "Photographie",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/14215'
					)
				);
				$va_simple_editor_menu_new_items["edit_separator"] = array(
					'displayName' => "<hr/>"
				);

				$va_simple_editor_menu_new_items["new_entity_ind"] = array(
					'displayName' => "Individu",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Entities',
						'action' => 'add/type_id/107'
					)
				);
				$va_simple_editor_menu_new_items["new_entity_org"] = array(
					'displayName' => "Institution",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Entities',
						'action' => 'add/type_id/108'
					)
				);
				$va_simple_editor_menu_new_items["new_expo"] = array(
					'displayName' => "Exposition",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Occurrences',
						'action' => 'add/type_id/140'
					)
				);
				$va_simple_editor_menu_new_items["new_biblio"] = array(
					'displayName' => "Bibliographie",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Occurrences',
						'action' => 'add/type_id/133'
					)
				);
				$va_simple_editor_menu_edit_items["edit_object"] = array(
					'displayName' => "Objet",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit'
					)
				);
				$va_simple_editor_menu_edit_items["edit_entity_ind"] = array(
					'displayName' => "Individu",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Entities',
						'action' => 'edit'
					)
				);
				$va_simple_editor_menu_edit_items["edit_entity_org"] = array(
					'displayName' => "Institution",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Entities',
						'action' => 'edit/type_id/108'
					)
				);
				$va_simple_editor_menu_edit_items["edit_occ_evt"] = array(
					'displayName' => "Exposition",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Occurrences',
						'action' => 'edit/type_id/140'
					)
				);
				$va_simple_editor_menu_edit_items["edit_occ_biblio"] = array(
					'displayName' => "Bibliographie",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Occurrences',
						'action' => 'edit/type_id/133'
					)
				);
				$pa_menu_bar_insert['simpleEditor_menu_new'] = array(
					'displayName' => _t('Nouveau'),
					'navigation' => $va_simple_editor_menu_new_items
				);
				$pa_menu_bar_insert['simpleEditor_menu_edit'] = array(
					'displayName' => _t('Fiches'),
					'navigation' => $va_simple_editor_menu_edit_items
				);
				unset($pa_menu_bar["New"]);
				$pa_menu_bar["find"]["displayName"] = "Parcourir";
				//var_dump($pa_menu_bar["find"]);
				//die();
				$pa_menu_bar = array_merge($pa_menu_bar_insert,$pa_menu_bar);
			} 
			//var_dump($pa_menu_bar);die();
			return $pa_menu_bar;
		}
		
		# -------------------------------------------------------
		/**
		 * Record editing activity
		 */
		public function hookEditItem($pa_params) {
			var_dump($pa_params);die();
			return $pa_params;
		}
		# -------------------------------------------------------
		/**
		 * Add plugin user actions
		 */
		static function getRoleActionList() {
			return array(
				'can_use_simple_editor_plugin' => array(
						'label' => _t('Can use simple editor functions'),
						'description' => _t('User can use simple editor.')
					)
			);
		}

		public function hookRenderWidgets($pa_widgets_config) {
			$pa_widgets_config["museesDeFranceRecolementInfoObjects"] = array(
				"domain" => array(
					"module" => "simpleEditor",
					"controller" => "Objects"),
				"handler" => array(
					"module" => "simpleEditor",
					"controller" => "Objects",
					"action" => 'SearchWidget',
					"isplugin" => true),
				"requires" => array(),
				"parameters" => array()
			);
			$pa_widgets_config["museesDeFranceRecolementInfoEntities"] = array(
				"domain" => array(
					"module" => "simpleEditor",
					"controller" => "Entities"),
				"handler" => array(
					"module" => "simpleEditor",
					"controller" => "Entities",
					"action" => 'SearchWidget',
					"isplugin" => true),
				"requires" => array(),
				"parameters" => array()
			);
			$pa_widgets_config["museesDeFranceRecolementInfoStorageLocations"] = array(
				"domain" => array(
					"module" => "simpleEditor",
					"controller" => "StorageLocations"),
				"handler" => array(
					"module" => "simpleEditor",
					"controller" => "StorageLocations",
					"action" => 'SearchWidget',
					"isplugin" => true),
				"requires" => array(),
				"parameters" => array()
			);
			$pa_widgets_config["museesDeFranceRecolementInfoOccurrences"] = array(
				"domain" => array(
					"module" => "simpleEditor",
					"controller" => "Occurrences"),
				"handler" => array(
					"module" => "simpleEditor",
					"controller" => "Occurrences",
					"action" => 'SearchWidget',
					"isplugin" => true),
				"requires" => array(),
				"parameters" => array()
			);
			return $pa_widgets_config;
		}

		# -------------------------------------------------------
		/**
		 * Insert into ObjectEditor info (side bar)
		 */
		public function hookAppendToEditorInspector(array $va_params = array())
		{
			//MetaTagManager::addLink('stylesheet', __CA_URL_ROOT__."/app/plugins//museesDeFrance/assets/css/museesDeFrance.css",'text/css');

			$t_item = $va_params["t_item"];
			//die("here");

			// basic zero-level error detection
			if (!isset($t_item)) return false;

			// fetching content of already filled vs_buf_append to surcharge if present (cumulative plugins)
			if (isset($va_params["vs_buf_append"])) {
				$vs_buf = $va_params["vs_buf_append"];
			} else {
				$vs_buf = "";
			}

			$vs_table_name = $t_item->tableName();
			$vn_item_id = $t_item->getPrimaryKey();
			if(method_exists($t_item,"getTypeCode")) {
				$vn_code = $t_item->getTypeCode();

				if ($vs_table_name == "ca_objects") {

					$vs_simpleEditor_url = caNavUrl($this->getRequest(), "simpleEditor", "Objects", "edit", array("object_id"=>$vn_item_id));

						$vs_buf = "<div style=\"margin:10px;border-radius:8px;padding:4px;background-color:#1ab3c8;color:white;text-align:center;\">"
							. "<a href=\"" . $vs_simpleEditor_url . "\" class='form-button-gradient' style='color:white;text-decoration:none;text-transform:uppercase;'>"
							. "Fiche objet"
							. "</a></div>";
				}
				
				if ($vs_table_name == "ca_entities") {

					$vs_simpleEditor_url = caNavUrl($this->getRequest(), "simpleEditor", "Entities", "edit", array("entity_id"=>$vn_item_id, "type_id"=>$t_item->getTypeID()));

						$vs_buf = "<div style=\"margin:10px;border-radius:8px;padding:4px;background-color:#1ab3c8;color:white;text-align:center;\">"
							. "<a href=\"" . $vs_simpleEditor_url . "\" class='form-button-gradient' style='color:white;text-decoration:none;text-transform:uppercase;'>"
							. "Fiche individu/institution"
							. "</a></div>";
				}

				if ($vs_table_name == "ca_occurrences") {

					$vs_simpleEditor_url = caNavUrl($this->getRequest(), "simpleEditor", "Occurrences", "edit", array("occurrence_id"=>$vn_item_id, "type_id"=>$t_item->getTypeID()));

						$vs_buf = "<div style=\"margin:10px;border-radius:8px;padding:4px;background-color:#1ab3c8;color:white;text-align:center;\">"
							. "<a href=\"" . $vs_simpleEditor_url . "\" class='form-button-gradient' style='color:white;text-decoration:none;text-transform:uppercase;'>"
							. "Fiche bibliographie/exposition"
							. "</a></div>";
				}

				$va_params["caEditorInspectorAppend"] = $vs_buf;
			}

			return $va_params;
		}
	}
?>