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

				$va_simple_editor_menu_new_items["new_object"] = array(
					'displayName' => "Nouvel objet",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/711'
					)
				);

				$va_simple_editor_menu_edit_items["edit_object"] = array(
					'displayName' => "Objet",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit/object_id/'
					)
				);

				$va_simple_editor_menu_new_items["new_entity_ind"] = array(
					'displayName' => "Personne physique",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/711'
					)
				);
				$va_simple_editor_menu_edit_items["edit_entity_ind"] = array(
					'displayName' => "Personne physique",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Entities',
						'action' => 'edit/entity_id/'
					)
				);
				$va_simple_editor_menu_new_items["new_place"] = array(
					'displayName' => "Lieu",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/711'
					)
				);
				$va_simple_editor_menu_edit_items["edit_place"] = array(
					'displayName' => "Lieu",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit/object_id/3476'
					)
				);
				$va_simple_editor_menu_new_items["new_location"] = array(
					'displayName' => "Emplacement de stockage",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/711'
					)
				);
				$va_simple_editor_menu_edit_items["edit_location"] = array(
					'displayName' => "Emplacement",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit/object_id/3476'
					)
				);
				$va_simple_editor_menu_new_items["new_coll"] = array(
					'displayName' => "Collection",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'add/type_id/711'
					)
				);
				$va_simple_editor_menu_edit_items["edit_coll"] = array(
					'displayName' => "Collection",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit/object_id/3476'
					)
				);
				$va_simple_editor_menu_edit_items["edit_occ_evt"] = array(
					'displayName' => "Evénement",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit/object_id/3476'
					)
				);
				$va_simple_editor_menu_edit_items["edit_occ_biblio"] = array(
					'displayName' => "Bibliographie",
					"default" => array(
						'module' => 'simpleEditor',
						'controller' => 'Objects',
						'action' => 'edit/object_id/3476'
					)
				);
				$pa_menu_bar_insert['simpleEditor_menu_edit'] = array(
					'displayName' => _t('Editeur'),
					'navigation' => $va_simple_editor_menu_edit_items
				);
				$pa_menu_bar_insert['simpleEditor_menu_new'] = array(
					'displayName' => _t('Nouveau'),
					'navigation' => $va_simple_editor_menu_new_items
				);
				unset($pa_menu_bar["New"]);
				$pa_menu_bar["find"]["displayName"] = "Traitements par lots";
				$pa_menu_bar = array_merge($pa_menu_bar_insert,$pa_menu_bar);
			} 
			//var_dump($pa_menu_bar);die();
			return $pa_menu_bar;
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
			$pa_widgets_config["museesDeFranceRecolementInfo"] = array(
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
			return $pa_widgets_config;
		}
	}
?>