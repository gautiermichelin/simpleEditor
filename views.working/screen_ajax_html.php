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
	$vs_subject_table   = get_class($t_object);
	$vn_object_id 		= ($this->getVar('subject_id') ? $this->getVar('subject_id') : $this->getVar('object_id'));
	$vn_screen_name		= ($this->getVar('screen_name') ? $this->getVar('screen_name') : $this->getVar('object_id'));
	$vn_above_id 		= $this->getVar('above_id');

	$vb_can_edit	 	= $t_object->isSaveable($this->request);
	$vb_can_delete		= $t_object->isDeletable($this->request);

	$vs_rel_table		= $this->getVar('rel_table');
	$vn_rel_type_id		= $this->getVar('rel_type_id');
	$vn_rel_id			= $this->getVar('rel_id');

	$vs_form_id         = ($this->getVar('form') ? $this->getVar('form') : "ObjectEditorForm");
	$vb_no_form_tag     = ($vs_form_id);

	$vs_last_selected_path_item = $this->getVar('last_selected_path_item');
	$vs_screen_name = str_replace("editAjax/","",$vs_last_selected_path_item);
//var_dump($vs_screen_name);die();

	//var_dump($vs_last_selected_path_item);die();
	//var_dump($vs_default_screen == $vs_last_selected_path_item);die();
	$va_screens = $this->getVar("screens");

switch($vs_subject_table) {
	case "ca_occurrences":
		$vs_classic_editor    = "/editor/occurrences/OccurrenceEditor";
		$vs_id_var          = "occurrence_id";
		$vs_simpleEditor_controller = "Occurrences";
		break;
	case "ca_storage_locations":
		$vs_classic_editor    = "/editor/storage_locations/StorageLocationEditor";
		$vs_id_var          = "location_id";
		$vs_simpleEditor_controller = "StorageLocations";
		break;
	case "ca_collections":
		$vs_classic_editor    = "/editor/collections/CollectionEditor";
		$vs_id_var          = "collection_id";
		$vs_simpleEditor_controller = "Collections";
		break;
	case "ca_places":
		$vs_classic_editor    = "/editor/places/PlaceEditor";
		$vs_id_var          = "place_id";
		$vs_simpleEditor_controller = "Places";
		break;
	case "ca_entities":
		$vs_classic_editor    = "/editor/entities/EntityEditor";
		$vs_id_var          = "entity_id";
		$vs_simpleEditor_controller = "Entities";
		break;
	case "ca_objects":
	default:
		$vs_classic_editor    = "/editor/objects/ObjectEditor";
		$vs_id_var          = "object_id";
		$vs_simpleEditor_controller = "Objects";
		break;
}

$colbreaks_by_screen = array(
	array("Screen193", 10),
	array("Screen194", 5),
	array("Screen67", 1),
	array("Screen61", 5),
	array("Screen62", 5),
	array("Screen185", 5)
);
$colwidth_by_screen = array(
	"Screen193"=>"50%",
	"Screen194"=>"50%",
	"Screen67"=>"50%",
	"Screen61"=>"50%",
	"Screen62"=>"50%",
	"Screen185"=>"50%"
);

$vn_subject_id = ($this->getVar('subject_id') ? $this->getVar('subject_id') : $this->getVar($vs_id_var));

	//print '<span style="color:#eeeeee;">'.$vs_simpleEditor_controller.'/Save/'.$vs_screen_name.'/'.$vs_id_var.'/'.$vn_subject_id.($vn_type_id ? "/type_id/".$vn_type_id : "")."</span>";
	// SIMPLE EDITOR SAVE
	////model : /index.php/simpleEditor/ObjectsAjax/Save/Screen194/object_id/4170
	print caFormTag($this->request, 'Save/'.$vs_screen_name.'/'.$vs_id_var.'/'.$vn_subject_id.($vn_type_id ? "/type_id/".$vn_type_id : ""), $vs_form_id, "simpleEditor/".$vs_simpleEditor_controller, 'POST', 'multipart/form-data');
	// SIMPLE EDITOR SAVE AJAX
	//print caFormTag($this->request, 'SaveAjax/'.$vs_screen_name.'/'.$vs_id_var.'/'.$vn_subject_id.($vn_type_id ? "/type_id/".$vn_type_id : ""), $vs_form_id, "simpleEditor/".$vs_simpleEditor_controller."Ajax", 'POST', 'multipart/form-data');
	// CLASSIC EDITOR SAVE
	//model2 : /index.php/editor/objects/ObjectEditor/Edit/Screen62/object_id/4170#
	//print caFormTag($this->request, 'Save/'.$vs_screen_name.'/object_id/'.$vn_object_id, $vs_form_id, "editor/objects/ObjectEditor", 'POST', 'multipart/form-data');

?>

<?php if($vs_form_id=="bottomform"): ?>
	<div>
		<a href="#" onclick=" jQuery(&quot;#<?php print $vs_form_id; ?>&quot;).submit();"
		   class="form-button 1457282139"><span class="form-button"><img
					src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_198_ok.png"
					border="0" class="form-button-left" style="padding-right: 10px"
					data-pin-nopin="true">Enregistrer</span></a>
	</div>
<?php endif; ?>
	<div id="form_inner">
<?php
	if ($vb_can_edit) {
		$va_cancel_parameters = ($vn_object_id ? array('object_id' => $vn_object_id) : array('type_id' => $t_object->getTypeID()));
	}

	if(!$vb_no_form_tag) {
		// Saving with default editor
		//print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/object_id/'.$vn_object_id, $vs_form_id, "editor/objects/ObjectEditor", 'POST', 'multipart/form-data');

		// Saving with simpleEditor
		//print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/object_id/'.$vn_object_id, $vs_form_id, "simpleEditor/Objects", 'POST', 'multipart/form-data');
	}


?>
		<div class="sectionBox">
			<div class="column" style="width:<?php print ($colwidth_by_screen[$vs_screen_name] ? : "100%");?>;float:left;">
	<?php

				$va_bundle_list = array();
				$va_form_elements = $t_object->getBundleFormHTMLForScreen($vs_screen_name, array(
										'request' => $this->request,
										'formName' => $vs_form_id,
										'forceHidden' => array('lot_id')
									), $va_bundle_list);
				//var_dump($va_form_elements);

				$i = 0;
				//print "<p><small>".$vs_screen_name."</small></p>";
				foreach($va_form_elements as $num=>$va_form_element) {
					// Top form column jump
					if(in_array(array($vs_screen_name, $i), $colbreaks_by_screen)) {
						// Migration
							print "</div><div class=\"column\" style=\"width:".($colwidth_by_screen[$vs_screen_name] ? : "100%").";float:left;\">";
					}
					print "<div class='dontsplit dontsplit_$i $num'>".$va_form_element."</div>    \n";
					$i++;
				}

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
	<?php if(($vs_form_id=="bottomform") & (($i>6) || ($vs_screen_name=="Screen64"))): ?>
		<div>
			<a href="#" onclick=" jQuery(&quot;#<?php print $vs_form_id; ?>&quot;).submit();"
			   class="form-button 1457282139"><span class="form-button"><img
						src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_198_ok.png"
						border="0" class="form-button-left" style="padding-right: 10px"
						data-pin-nopin="true">Enregistrer</span></a>
		</div>
	<?php endif; ?>

