function SearchByModel(res)
{
  if (res.data.known == false)
  {
    $('#model').tooltip({'title': 'Артикул неизвестен. Нужно привязать к карточке модели.'}).tooltip('show');
    $('#buttons').html(phoxy.DeferRender('admin/storage_barcode_bind', {'barcode': $('#model').val()}));
  }
  else
  {
    $('#model').tooltip('destroy');
    $('#buttons').html('');
  }
}

function SearchByIMEI(res)
{
  if (res.data.known == false)
  {
    $('#imei').tooltip({'title': 'IMEI неизвестен. Нужно привязать к артикулу.', 'trigger': ''}).tooltip('show');
    phoxy.Defer(function()
    {
      $('#model').focus();
      $('#link-button').show();
      $('#imei').tooltip('destroy');
    }, 1000);
  }
}

function BindImeiAndModel(res)
{
  if (res.data.binded == true)
  {
    $('#link-button').hide();
    $('#imei').tooltip('destroy');
  }
}

function BindBarcodeAndModel(res)
{
  
}
