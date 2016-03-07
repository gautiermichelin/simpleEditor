<?php
    require_once(__CA_APP_DIR__."/plugins/simpleEditor/helpers/displayHelpers.php");

    $vn_object_id	= $this->getVar('object_id');
    //var_dump($this->getVar("t_item"));
    if ($this->getVar("t_item")) {
        //
        //die();
    }


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
    <H1>Objet <?php print $vn_object_id; ?></H1>
    <div style="border:1px solid blue;clear:both;">
        <div style="width:33%;border:1px solid green;float:right;">
            <?php print caSimpleEditorInspector($this); ?>
        </div>
        <div style="width:65%;border:1px solid red;">
            form-top
        </div>
    </div>
</div>
<div style="border:1px solid blue;clear:both;">Menu avec liste des écrans</div>
<div style="border:1px solid blue;">Partie basse</div>

