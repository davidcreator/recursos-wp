(function($){
  $(function(){
    $('.wpsp-setup-authenticator').on('click', function(){
      var userId = $(this).data('user-id');
      $.post(wpsp2FA.ajaxurl, {action:'wpsp_setup_authenticator', user_id:userId, nonce:wpsp2FA.nonce}, function(r){
        if(r.success){
          $('#wpsp-qr-code').html('<img src="'+r.data.qr_code_url+'" alt="QR">');
        } else {
          alert(r.data && r.data.message ? r.data.message : 'Error');
        }
      });
    });
    $('.wpsp-generate-backup-codes').on('click', function(){
      var userId = $(this).data('user-id');
      $.post(wpsp2FA.ajaxurl, {action:'wpsp_generate_backup_codes', user_id:userId, nonce:wpsp2FA.nonce}, function(r){
        if(r.success){
          var html = '<ul>';
          for(var i=0;i<r.data.codes.length;i++){ html += '<li>'+r.data.codes[i]+'</li>'; }
          html += '</ul>';
          $('#wpsp-backup-codes').html(html);
        } else {
          alert(r.data && r.data.message ? r.data.message : 'Error');
        }
      });
    });
  });
})(jQuery);
