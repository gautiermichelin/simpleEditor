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
	$vs_subject_table    = get_class($t_object);
	$vn_type_id         = $this->getVar('type_id');
	$vn_above_id 		= $this->getVar('above_id');

	$vb_can_edit	 	= $t_object->isSaveable($this->request);
	$vb_can_delete		= $t_object->isDeletable($this->request);

	$vs_rel_table		= $this->getVar('rel_table');
	$vn_rel_type_id		= $this->getVar('rel_type_id');
	$vn_rel_id			= $this->getVar('rel_id');


	$vs_last_selected_path_item = str_ireplace("edit/","",$this->getVar('last_selected_path_item'));
	$vs_default_screen 	= $this->getVar('default_screen');

	$va_screens = $this->getVar("screens");

	// Getting first screen of non default ones
	$vs_screen_code = key($va_screens);
	$vs_first_non_default_screen = str_replace("screen_","Screen",$vs_screen_code);

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

	$vn_subject_id = ($this->getVar('subject_id') ? $this->getVar('subject_id') : $this->getVar($vs_id_var));
?>
	<div id="topNavSecondLine"><div id="toolIcons">
			<script type="text/javascript">
				function caToggleItemWatch() {
					var url = '<?php print __CA_URL_ROOT__; ?>/index.php<?php print $vs_classic_editor."/toggleWatch/".$vs_id_var."/".$vn_subject_id ?>';
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
		
	</div>
	<div id="simple_editor_top">
		<div id="top_box">
			<div id="medias_box">
				<?php if($vn_subject_id) {
					print caSimpleEditorInspector($this);
				}  ?>
			</div>
			<div id="top_editor_box">
				<div class="control-box rounded" id="topButtons">
					<a href="#" class="form-button 1457282139" onclick="topformSubmitClicked();" id="<?php print $vs_form_id; ?>_submit"><span class="form-button"><img
								src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_198_ok.png"
								border="0" class="form-button-left" style="padding-right: 10px"
								data-pin-nopin="true">Enregistrer</span></a>
					<?php if ($vn_subject_id): ?>
						<a href="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/Objects/Edit/<?php print $vs_last_selected_path_item; ?>/object_id/<?php print $vn_subject_id; ?>"
						   class="form-button"><span class="form-button "><img
									src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_445_floppy_remove.png"
									alt="Annuler" title="Annuler" border="0" class="cancelIcon"
									style="padding-right: 10px;" data-pin-nopin="true">Annuler</span></a>
						<div class="watchThis"><a href="#" title="Surveiller cet enregistrement"
												  onclick="caToggleItemWatch(); return false;" id="caWatchItemButton"><img
									src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_051_eye_open_gray.png"
									alt="glyphicons_051_eye_open_gray" border="0"> Surveiller</a>
						</div>
						<div id="caDuplicateItemButton" title="Duplique cet objet">
							<form action="<?php print __CA_URL_ROOT__; ?>/index.php/editor/objects/ObjectEditor/Edit"
								  method="post" id="DuplicateItemForm" target="_top" enctype="multipart/form-data">
								<input type="hidden" name="_formName" value="DuplicateItemForm">
								<a href="#" onclick="document.getElementById(&quot;DuplicateItemForm&quot;).submit();"
								   class=""><img
										src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_318_more_items.png"
										alt="glyphicons_318_more_items" border="0">Dupliquer</a><input name="object_id"
																									   value="<?php print $vn_subject_id; ?>"
																									   type="hidden">
								<input name="mode" value="dupe" type="hidden">
							</form>
						</div>
							<a class="form-button 1457282139" href="<?php print __CA_URL_ROOT__; ?>/index.php/editor/objects/ObjectEditor/PrintSummary/object_id/<?php print $vn_subject_id; ?>">
								<span class="form-button">
									<img
									src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_359_file_export.png"
									alt="glyphicons_359_file_export" border="0" data-pin-nopin="true"> PDF
								</span>
							</a>
						<a
							href="<?php print __CA_URL_ROOT__; ?>/index.php/editor/objects/ObjectEditor/Delete/Screen65/object_id/<?php print $vn_subject_id; ?>"
							class="form-button deleteButton"><span class="form-button "><img
									src="<?php print __CA_THEME_URL__; ?>/graphics/buttons/glyphicons_199_ban.png"
									alt="Supprimer" title="Supprimer" border="0" class="deleteIcon"
									style="padding-right: 10px;">Supprimer</span></a>
						<?php endif; ?>
				</div>

				<?php
				// Saving with default editor
				//print caFormTag($this->request, 'Save/'.$vs_default_screen.'/'.$vs_id_var.'/'.$vn_subject_id.($vn_type_id ? "/type_id/".$vn_type_id : ""), 'topform', "simpleEditor/".$vs_simpleEditor_controller, 'POST', 'multipart/form-data');

				// Saving with simpleEditor
				//print caFormTag($this->request, 'Save/'.$vs_default_screen.'/object_id/'.$vn_object_id, 'ObjectEditorTopForm', "simpleEditor/Objects", 'POST', 'multipart/form-data');

				// Saving with simpleEditor
				//print caFormTag($this->request, 'Save/'.$vs_default_screen.'/object_id/'.$vn_object_id, 'ObjectEditorTopForm', "simpleEditor/Objects", 'POST', 'multipart/form-data');

				?>
				<div class="bundles" id="top_form">

				</div>
				<!--</form>-->

			</div>
		</div>
	</div>

	<div id="screensList">
<?php
	foreach($va_screens as $va_screen) {
		// We display only the non-default screens, as the default one is on top left of the screen
		if (($va_screen["default"]["action"] == $vs_last_selected_path_item) || ($va_screen["default"]["action"] == "edit/".$vs_last_selected_path_item )){
			// Current loaded screen
			$vs_class="screen_button current";
		} else {
			$vs_class="screen_button";
		}
		//print caNavLink($this->request, $va_screen["displayName"], $vs_class, "*", "*", $va_screen["default"]["action"],array("object_id"=>$vn_object_id) );
		$vs_screen_name = str_ireplace("edit/","", $va_screen["default"]["action"]);
		$vs_screen_name = str_ireplace("save/","", $vs_screen_name);
		print "<a onclick=\"toggleSimpleEditorLowerForm('".$vs_simpleEditor_controller."Ajax"."','".$vs_screen_name."','".$vs_id_var."/".$vn_subject_id."','bottomform');\" class=\"".$vs_class." ".$vs_screen_name."\">".$va_screen["displayName"]."</a>";
	}
?>
	</div>
	<div id="lower_form">
	</div>
	<div class="editorBottomPadding"><!-- empty --></div>
	
<?php
	print caSetupEditorScreenOverlays($this->request, $t_object, $va_bundle_list);
	//Temporary disabling extraction of current screen
	//$this_screen = ($vs_last_selected_path_item ?  $vs_last_selected_path_item : $vs_first_non_default_screen);
	if($vs_last_selected_path_item != $vs_default_screen) {
		$this_screen = $vs_last_selected_path_item;
	} else {
		$this_screen = $vs_first_non_default_screen;
	}


?>

<script type="text/javascript">
	function toggleSimpleEditorLowerForm(ajaxController,screenname,id,form) {
	    var url = "<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/"+ajaxController+"/editAjax/"+screenname+"/"+id+"/form/"+form;
	    console.log("Refreshing lower_form with "+url);
	    jQuery.ajax({
	      url: url,
	      cache: false
	    }).done(function( html ) {
	        jQuery( "#lower_form" ).html(html);
	        jQuery( "#lower_form" ).css("position","fixed");
	        jQuery( "#lower_form" ).fadeIn();
	    });
	}

	function loadSimpleEditorTopForm(ajaxController,screenname,id,form) {
	    var url = "<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/"+ajaxController+"/editAjax/"+screenname+"/"+id+"/form/"+form;
	    console.log("Refreshing top_form with "+url);
	    jQuery.ajax({
	      url: url,
	      cache: false
	    }).done(function( html ) {
	        jQuery( "#top_form" ).html(html);
	        jQuery( "#top_form" ).fadeIn();
	    });


	}

	jQuery(document).ready(function(){
		loadSimpleEditorTopForm('<?php print $vs_simpleEditor_controller."Ajax"; ?>', '<?php print $vs_default_screen; ?>','<?php print "/".$vs_id_var."/".$vn_subject_id; ?>','topform');
		jQuery('#screensList').find("A").removeClass('current');
		jQuery('#screensList').find("A.<?php print $this_screen; ?>").addClass('current');
		window.setTimeout(toggleSimpleEditorLowerForm, 100,'<?php print $vs_simpleEditor_controller."Ajax"; ?>', '<?php print $this_screen; ?>','<?php print "/".$vs_id_var."/".$vn_subject_id; ?>','bottomform');

	    jQuery("#top_editor_box .bundles").fadeIn();

	    jQuery("#screensList a").on("click",function(){
	        jQuery("#screensList").find("A").removeClass('current');
	        jQuery(this).addClass('current');
	        jQuery( "#lower_form" ).hide();
	    });
	});
</script>
