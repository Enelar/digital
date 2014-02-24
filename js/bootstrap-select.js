function RequireBootstrapSelect()
{
  phoxy
    .ApiAnswer(
    {
      'script' : ['bootstrap-select/bootstrap-select.js']
    },
    function()
    {
      $('body').append('<link rel="stylesheet" type="text/css" href="bootstrap-select/bootstrap-select.min.css">');

      $('.selectpicker').selectpicker();
    });
}

RequireBootstrapSelect();
