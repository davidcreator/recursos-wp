(function($){
  $(function(){
    $('.wpsp-enable-2fa').on('click', function(){
      var method = $(this).data('method');
      var userId = $(this).data('user-id');
      $.post(wpspAdmin.ajaxurl, {action:'wpsp_enable_2fa', method:method, user_id:userId, nonce:wpspAdmin.nonce}, function(r){
        alert(r.success ? r.data.message : (r.data && r.data.message ? r.data.message : 'Error'));
        location.reload();
      });
    });
  });
})(jQuery);
