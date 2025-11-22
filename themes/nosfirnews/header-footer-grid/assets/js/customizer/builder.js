(function(api){
  function setHeaderBg(color){var el=document.querySelector('.site-header');if(el)el.style.backgroundColor=color}
  api('nosfirnews_hfg_header_bg',function(value){value.bind(function(v){setHeaderBg(v)})})
})(wp.customize);