<?php
/**
 * Created by PhpStorm.
 * User: gautier
 * Date: 06/03/2016
 * Time: 22:52
 */

# ------------------------------------------------------------------------------------------------
/**
 * Generates standard-format inspector panels for editors
 *
 * @param View $po_view Inspector view object
 * @param array $pa_options Optional array of options. Supported options are:
 *		backText = a string to use as the "back" button text; default is "Results"
 *
 * @return string HTML implementing the inspector
 */
function caSimpleEditorInspector($po_view, $pa_options=null) {
    require_once(__CA_MODELS_DIR__.'/ca_sets.php');
    require_once(__CA_MODELS_DIR__.'/ca_data_exporters.php');

    $t_item 				= $po_view->getVar('t_item');
    //var_dump($t_item->get("ca_objects.idno"));die();
    //$vs_table_name = $t_item->tableName();
    if (($vs_priv_table_name = $vs_table_name) == 'ca_list_items') {
        $vs_priv_table_name = 'ca_lists';
    }

    $va_reps= $po_view->getVar('representations');

    //var_dump($va_reps);die();

    $o_dm = Datamodel::load();

    $vs_buf = "";
    $va_imgs = array();
    if (sizeof($va_reps) > 0) {

        $vn_r = $vn_primary_index = 0;
        foreach ($va_reps as $va_rep) {
            if (!($va_rep['info']['preview170']['WIDTH'] && $va_rep['info']['preview170']['HEIGHT'])) {
                continue;
            }

            if ($vb_is_primary = (isset($va_rep['is_primary']) && (bool)$va_rep['is_primary'])) {
                $vn_primary_index = $vn_r;
                $va_img_primary = $va_rep['urls']['medium'];
            }
            $va_imgs[$vn_r++] = $va_rep['urls']['medium'];
            $vn_r++;
        }
    }

    if($va_img_primary) {
        $vs_buf .= "<div id=\"simple_editor_main_img\" class=\"simple_editor_img_primary\" style=\"background-image:url($va_img_primary);background-size:contain;background-position:50% 50%;background-repeat:no-repeat;\" onclick='caMediaPanel.showPanel(\"/index.php/find/SearchObjects/QuickLook/object_id/3476\"); return false;' ><span class=\"helper\"></span></div>";
        //$vs_buf .= "<div><a class='qlButton' onclick='caMediaPanel.showPanel(\"/index.php/find/SearchObjects/QuickLook/object_id/3476\"); return false;' >Quick Look</a></div>";

    }
    if (sizeof($va_imgs)) {
        foreach($va_imgs as $va_img) {
            $vs_imgs_buf .= "<img src=".$va_img.">";
        }
        $vs_buf .= "<div class='simple_editor_imgs'>".$vs_imgs_buf."</div>";
    }

    $vs_buf .= "
    <script type='text/javascript'>
        jQuery(document).ready(function() {
            jQuery('.simple_editor_imgs IMG').on('click', function(){
                var clicked_image_src = jQuery(this).attr('src');
                var main_image = jQuery('#simple_editor_main_img');
                main_image.css('background-image', 'url('+clicked_image_src+')');
            });
        });
    </script>
    ";

    return $vs_buf;
}
# ------------------------------------------------------------------------------------------------
