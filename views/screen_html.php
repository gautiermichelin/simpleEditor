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
	$vn_above_id 		= $this->getVar('above_id');

	$vb_can_edit	 	= $t_object->isSaveable($this->request);
	$vb_can_delete		= $t_object->isDeletable($this->request);

	$vs_rel_table		= $this->getVar('rel_table');
	$vn_rel_type_id		= $this->getVar('rel_type_id');
	$vn_rel_id			= $this->getVar('rel_id');


	$vs_last_selected_path_item = $this->getVar('last_selected_path_item');
	$vs_default_screen 	= $this->getVar('default_screen');
	$va_screens = $this->getVar("screens");

	print caFormTag($this->request, 'Save/'.$this->request->getActionExtra().'/object_id/'.$vn_object_id, 'ObjectEditorForm', null, 'POST', 'multipart/form-data');

?>

	<div id="topNavSecondLine"><div id="toolIcons">
			<script type="text/javascript">
				function caToggleItemWatch() {
					var url = '<?php print __CA_URL_ROOT__; ?>/index.php/editor/objects/ObjectEditor/toggleWatch/object_id/<?php print $vn_object_id ?>';
					console.log(url);
					jQuery.getJSON(url, {}, function(data, status) {
						if (data['status'] == 'ok') {
							jQuery('#caWatchItemButton').html(
								(data['state'] == 'watched') ?
									'<img src=\'<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_051_eye_open_small.png\' alt=\'glyphicons_051_eye_open_small\' border=\'0\' />Ne plus surveiller' :
									'<img src=\'<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_051_eye_open_gray.png\' alt=\'glyphicons_051_eye_open_gray\' border=\'0\' />Surveiller'
							);
						} else {
							console.log('Error toggling watch status for item: ' + data['errors']);
						}
					});
				}
			</script>

		</div>
		<div class="control-box rounded" id="topButtons">
			<div class="control-box-left-content">
				<a href="#" onclick=" jQuery(&quot;#ObjectEditorForm&quot;).submit();" class="form-button 1457282139"><span class="form-button"><img src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_198_ok.png" border="0" class="form-button-left" style="padding-right: 10px" data-pin-nopin="true">Enregistrer</span></a><div style="position: absolute; top: 0px; left:-5000px;"><input type="submit"></div>  <a href="<?php print __CA_NAV_URL; ?>/editor/objects/ObjectEditor/Edit/Screen65/object_id/2" class="form-button"><span class="form-button "><img src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_445_floppy_remove.png" alt="Annuler" title="Annuler" border="0" class="cancelIcon" style="padding-right: 10px;" data-pin-nopin="true">Annuler</span></a></div>
			<div class="watchThis"><a href="#" title="Surveiller cet enregistrement" onclick="caToggleItemWatch(); return false;" id="caWatchItemButton"><img src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_051_eye_open_gray.png" alt="glyphicons_051_eye_open_gray" border="0"> Surveiller</a></div>
			<div id="caDuplicateItemButton" title="Duplique cet objet">
				<form action="<?php print __CA_URL_ROOT__; ?>/index.php/editor/objects/ObjectEditor/Edit" method="post" id="DuplicateItemForm" target="_top" enctype="multipart/form-data">
					<input type="hidden" name="_formName" value="DuplicateItemForm">
					<a href="#" onclick="document.getElementById(&quot;DuplicateItemForm&quot;).submit();" class=""><img src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_318_more_items.png" alt="glyphicons_318_more_items" border="0">Dupliquer</a><input name="object_id" value="<?php print $vn_object_id; ?>" type="hidden">
					<input name="mode" value="dupe" type="hidden">
				</form>
			</div>

			<div class="control-box-right-content"><a href="<?php print __CA_NAV_URL; ?>/editor/objects/ObjectEditor/Delete/Screen65/object_id/<?php print $vn_object_id; ?>" class="form-button deleteButton"><span class="form-button "><img src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_199_ban.png" alt="Supprimer" title="Supprimer" border="0" class="deleteIcon" style="padding-right: 10px;">Supprimer</span></a></div><div class="control-box-middle-content"></div>
		</div>
	</div>
	<div id="simple_editor_top">
		<div id="top_box">
			<div id="medias_box" style="min-height:230px;">
				<?php print caSimpleEditorInspector($this); ?>
			</div>
			<div id="top_editor_box">
				<?php
				$va_form_elements = $t_object->getBundleFormHTMLForScreen($vs_default_screen, array(
					'request' => $this->request,
					'formName' => 'ObjectEditorForm',
					'forceHidden' => array('lot_id')
				), $va_bundle_list);

				print join("\n", $va_form_elements);

				?>
			</div>
		</div>
	</div>
	<div id="screensList">
<?php

	foreach($va_screens as $va_screen) {
		// We display only the non-default screens, as the default one is on top left of the screen
		if (($va_screen["default"]["action"] == $vs_last_selected_path_item) || ($va_screen["default"]["action"] == "Edit/".$vs_last_selected_path_item )){
			// Current loaded screen
			$vs_class="screen_button current";
		} else {
			$vs_class="screen_button";
		}
		print caNavLink($this->request, $va_screen["displayName"], $vs_class, "*", "*", $va_screen["default"]["action"],array("object_id"=>$vn_object_id) );
	}
?>
	</div>
	<div id="lower_form">
<?php
	if ($vb_can_edit) {
		$va_cancel_parameters = ($vn_object_id ? array('object_id' => $vn_object_id) : array('type_id' => $t_object->getTypeID()));
	}
?>
		<div class="sectionBox">
	<?php

				$va_bundle_list = array();
				$va_form_elements = $t_object->getBundleFormHTMLForScreen(str_replace("Edit/","",$vs_last_selected_path_item), array(
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
	</div>
	</form>
	<div class="editorBottomPadding"><!-- empty --></div>
	
	<?php print caSetupEditorScreenOverlays($this->request, $t_object, $va_bundle_list); ?>