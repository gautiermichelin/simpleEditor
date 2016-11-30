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
 	require_once(__CA_MODELS_DIR__.'/ca_places.php');
 	require_once(__CA_MODELS_DIR__.'/ca_object_representations_x_places.php');
 	require_once(__CA_MODELS_DIR__.'/ca_locales.php');


	require_once(__CA_LIB_DIR__."/ca/Search/PlaceSearch.php");
	require_once(__CA_LIB_DIR__."/ca/Search/PlaceSearchResult.php");

	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseController.php");
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorBaseAjaxController.php");
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/controllers/SimpleEditorCommonAjaxController.php");

	class OccurrencesAjaxController extends SimpleEditorCommonAjaxController
	{

		# -------------------------------------------------------
		public function __construct(&$po_request, &$po_response, $pa_view_paths = null)
		{
			parent::__construct($po_request, $po_response, $pa_view_paths);
			$this->ops_table_name = 'ca_occurrences';        // name of "subject" table (what we're editing)
			$this->ops_list_type_id = 'occurrence_types';
			$this->ops_table_field_id = 'occurrence_id';
		}
	}