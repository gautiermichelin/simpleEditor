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
    $vs_table_name = $t_item->tableName();
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
                $va_img_primary = $va_rep['urls']['preview170'];
            }
            $va_imgs[$vn_r++] = $va_rep['urls']['preview170'];
            $vn_r++;
        }
    }

    $vs_buf .= "<div id=\"simple_editor_main_img\" class=\"simple_editor_img_primary\"><img src=$va_img_primary /></div><p>";
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
                console.log(clicked_image_src);
                console.log(jQuery('#simple_editor_main_img').attr('src'));
                var main_image = jQuery('#simple_editor_main_img > IMG');

                //main_image.attr('src',clicked_image_src);
                main_image.fadeOut('fast', function () {
                    main_image.attr('src', clicked_image_src);
                    main_image.fadeIn('fast');
                });
            });
        });
    </script>
    ";

    return $vs_buf;
}
# ------------------------------------------------------------------------------------------------
