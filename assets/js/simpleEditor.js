// Simple binary to know if we need to recharge screen, defaulting to no error
var ca_simpleeditor_form_errors=0;
var ca_simpleeditor_topformSaveTreated=0;
var ca_simpleeditor_bottomformSaveTreated=0;

var ca_simpleeditor_refreshIfNoError = function() {
    console.log("refreshIfNoError");
    if((ca_simpleeditor_topformSaveTreated==1) && (ca_simpleeditor_bottomformSaveTreated==1)) {
        if (ca_simpleeditor_form_errors==0) {
            location.reload();
        }
    }
}

var topformSubmitClicked = function() {
    jQuery("div.notification-info-box").remove();
    jQuery.ajax({
        url: jQuery("#bottomform").attr('action'), // Le nom du fichier indiqué dans le formulaire
        type: jQuery("#bottomform").attr('method'), // La méthode indiquée dans le formulaire (get ou post)
        data: jQuery("#bottomform").serialize(), // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
        dataType: "json"
    }).done(function(msg) { // Je récupère la réponse du fichier PHP
        var notificationClass;
        for(let notification of msg.notifications) {
            console.log(notification);
            notificationClass="";
            if(notification.type == 2) {
                notificationClass="notification-info-box";
            }
            if(notification.type == 1) {
                notificationClass="notification-warning-box";
                ca_simpleeditor_form_errors++;
                console.log("errors in current form : "+ca_simpleeditor_form_errors);
            }
            if(notification.type == 0) {
                notificationClass="notification-error-box";
                ca_simpleeditor_form_errors++;
                console.log("errors in current form : "+ca_simpleeditor_form_errors);
            }
            if((notification.type == 0) || (notification.type == 1)) {
                jQuery("#mainContent").prepend('<div class="'+notificationClass+' rounded"><ul class="'+notificationClass+'"><li class="'+notificationClass+'">'+notification.message+'</li></ul></div>');
            }
        }
        console.log("posted with success");
        //alert(msg); // J'affiche cette réponse
        ca_simpleeditor_topformSaveTreated = 1;
        ca_simpleeditor_refreshIfNoError();
    }).fail(function(msg) {
        console.log(msg);
        alert("error");
    });
    jQuery.ajax({
        url: jQuery("#topform").attr('action'), // Le nom du fichier indiqué dans le formulaire
        type: jQuery("#topform").attr('method'), // La méthode indiquée dans le formulaire (get ou post)
        data: jQuery("#topform").serialize(), // Je sérialise les données (j'envoie toutes les valeurs présentes dans le formulaire)
        dataType: "json"
    }).done(function(msg) { // Je récupère la réponse du fichier PHP
        var notificationClass;
        for(let notification of msg.notifications) {
            console.log(notification);
            notificationClass="";
            if(notification.type == 2) {
                notificationClass="notification-info-box";
            }
            if(notification.type == 1) {
                notificationClass="notification-warning-box";
                ca_simpleeditor_form_errors++;
                console.log("errors in current form : "+ca_simpleeditor_form_errors);
            }
            if(notification.type == 0) {
                notificationClass="notification-error-box";
                ca_simpleeditor_form_errors++;
                console.log("errors in current form : "+ca_simpleeditor_form_errors);
            }
            jQuery("#mainContent").prepend('<div class="'+notificationClass+' rounded"><ul class="'+notificationClass+'"><li class="'+notificationClass+'">'+notification.message+'</li></ul></div>');

        }
        console.log("posted with success");
        ca_simpleeditor_bottomformSaveTreated=1;
        ca_simpleeditor_refreshIfNoError();
        //alert(msg); // J'affiche cette réponse
    }).fail(function(msg) {
        console.log(msg);
        alert("error");
    });

    jQuery("#lower_form").hide();
    jQuery("#lower_form").show();
}