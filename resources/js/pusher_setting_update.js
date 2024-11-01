(function() {
    jQuery('#pusher_verify_button').click(function(e) {
        let pusher_data = {
            app_id: jQuery('#pusher_app_id').val(),
            app_key: jQuery('#pusher_app_key').val(),
            secret_key: jQuery('#pusher_secret_key').val(),
            cluster_key: jQuery('#pusher_cluster_key').val(),
        }
       
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                action: 'general_pusher_verify',
                nonce: jQuery('input[name=wschat_settings_nonce]').val(),
                p_data: pusher_data,
            },
            success: function(data){
                if(data){
                    alert('Credentials successfully verified and saved.');

                }else{
                    alert('Unable to connect to the pusher. Please validate the credentials and try again');
                }
            }
        });
    });

})();