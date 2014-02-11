function SearchByModel(res)
{
  if (res.data.known == false)
  {
    $('#model').tooltip({'title': 'Артикул неизвестен. Нужно привязать к карточке модели.'});
  }
}

function SearchByIMEI(res)
{
  if (res.data.known == false)
  {
    $('#imei').tooltip({'title': 'IMEI неизвестен. Нужно привязать к артикулу.'});
    $('#model').focus();
  }
}
