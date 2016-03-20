<?php
/* ----------------------------------------------------------------------
 * views/editor/objects/screen_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2013 Whirl-i-Gig
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
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/helpers/displayHelpers.php");

 	$t_object 			= $this->getVar('t_subject');
	$vn_object_id 		= ($this->getVar('subject_id') ? $this->getVar('subject_id') : $this->getVar('object_id'));
	$vn_screen_name		= ($this->getVar('screen_name') ? $this->getVar('screen_name') : $this->getVar('object_id'));
	$vn_above_id 		= $this->getVar('above_id');

	$vb_can_edit	 	= $t_object->isSaveable($this->request);
	$vb_can_delete		= $t_object->isDeletable($this->request);

	$vs_rel_table		= $this->getVar('rel_table');
	$vn_rel_type_id		= $this->getVar('rel_type_id');
	$vn_rel_id			= $this->getVar('rel_id');

	$vs_form_id         = ($this->getVar('form') ? $this->getVar('form') : "ObjectEditorForm");
	$vb_no_form_tag        = ($vs_form_id == "none");

	$vs_last_selected_path_item = $this->getVar('last_selected_path_item');
	$vs_screen_name = str_replace("editAjax/","",$vs_last_selected_path_item);
	//var_dump($vs_last_selected_path_item);die();
	//var_dump($vs_default_screen == $vs_last_selected_path_item);die();
	$va_screens = $this->getVar("screens");

	//print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/object_id/'.$vn_object_id, 'ObjectEditorForm', null, 'POST', 'multipart/form-data');

?>
	<div id="form_inner">
<?php
	if ($vb_can_edit) {
		$va_cancel_parameters = ($vn_object_id ? array('object_id' => $vn_object_id) : array('type_id' => $t_object->getTypeID()));
	}

	if(!$vb_no_form_tag) {
		// Saving with default editor
		//print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/object_id/'.$vn_object_id, $vs_form_id, "editor/objects/ObjectEditor", 'POST', 'multipart/form-data');

		// Saving with simpleEditor
		print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/object_id/'.$vn_object_id, $vs_form_id, "simpleEditor/Objects", 'POST', 'multipart/form-data');
	}


?>
		<div class="sectionBox">
	<?php

				$va_bundle_list = array();
				$va_form_elements = $t_object->getBundleFormHTMLForScreen($vs_screen_name, array(
										'request' => $this->request,
										'formName' => 'ObjectEditorForm',
										'forceHidden' => array('lot_id')
									), $va_bundle_list);

				print join("\n", $va_form_elements);

				if ($vb_can_edit) { print $vs_control_box; }
	?>
				<input type='hidden' name='object_id' value='<?php print $vn_object_id; ?>'/>
				<input type='hidden' name='collection_id' value='<?php print $this->request->getParameter('collection_id', pInteger); ?>'/>
				<input type='hidden' name='above_id' value='<?php print $vn_above_id; ?>'/>
				<input id='isSaveAndReturn' type='hidden' name='is_save_and_return' value='0'/>
				<input type='hidden' name='rel_table' value='<?php print $vs_rel_table; ?>'/>
				<input type='hidden' name='rel_type_id' value='<?php print $vn_rel_type_id; ?>'/>
				<input type='hidden' name='rel_id' value='<?php print $vn_rel_id; ?>'/>
	<?php
				if($this->request->getParameter('rel', pInteger)) {
	?>
					<input type='hidden' name='rel' value='1'/>
	<?php
				}
	?>
		</div>
		</form>
	</div>

	<?php print caSetupEditorScreenOverlays($this->request, $t_object, $va_bundle_list); ?>