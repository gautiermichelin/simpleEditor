function toggleSimpleEditorLowerForm(ajaxController,screenname,id) {
    var url = "/index.php/simpleEditor/"+ajaxController+"/editAjax/"+screenname+id;
    console.log("Refreshing lower_form with simpleEditor/"+ajaxController+"/editAjax/"+screenname+id+"...");
    jQuery.ajax({
      url: url,
      cache: false
    }).done(function( html ) {
        jQuery( "#lower_form" ).html(html);
        jQuery( "#lower_form" ).css("position","fixed");
        jQuery( "#lower_form" ).fadeIn();
    });
}

function loadSimpleEditorTopForm(ajaxController,screenname,id) {
    var url = "/index.php/simpleEditor/"+ajaxController+"/editAjax/"+screenname+id+"/form/none";
    console.log("Refreshing top_form with "+url);
    jQuery.ajax({
      url: url,
      cache: false
    }).done(function( html ) {
        jQuery( "#top_form" ).html(html);
        jQuery( "#top_form" ).fadeIn();
    });
}


jQuery(document).ready(function() {
    jQuery("#top_editor_box .bundles").fadeIn();
    jQuery("#screensList a").on("click",function(){
        jQuery("#screensList").find("A").removeClass('current');
        jQuery(this).addClass('current');
        jQuery( "#lower_form" ).hide();
    });
});