(function(){
  function total(a,b){ return (parseFloat(a)||0)+(parseFloat(b)||0); }
  window.NNLayout={ validateWidths:function(content,sidebar){ return total(content,sidebar)<=100; } };
})();
