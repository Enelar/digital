var phone_info = 
{
  params : {},
  values : {}
}

function BindPhoneInfo(answer, callback)
{
  function SelfFunctor()
  {
    BindPhoneInfo(answer, callback);
  };
  
  var i;

  var values_to_request = [], params_to_request = [];
  for (i = 0; i < answer.data.params.length; i++)
  {
    var id = parseInt(answer.data.params[i]);
    if (phone_info.values[id] === undefined)
      values_to_request.push(id);
    else
    {
      var k = phone_info.values[id].k;
      if (phone_info.params[k] === undefined)
        params_to_request.push(k);
    }
  }
  
  if (values_to_request.length)
    return RequestValues(values_to_request, SelfFunctor);
  if (params_to_request.length)
    return RequestParams(params_to_request, SelfFunctor);
  
  var binded = {};
  
  for (i = 0; i < answer.data.params.length; i++)
  {
    var id = parseInt(answer.data.params[i]);
    var value = phone_info.values[id];
    var param = phone_info.params[value.k];
    
    binded[param.name] = {v: value.v, hide: param.hide};
  }
  
  answer.data.params = binded;
  callback(answer);
}

function ToGetArr( ids )
{
  var ret = "?";
  for (var i = 0; i < ids.length; i++)
    ret += "a[]=" + ids[i] + "&";
  return ret;
}

function RequestValues(ids, callback)
{
  function Callback(data)
  {
    for (var k in data.data.values)
      phone_info.values[k] = data.data.values[k];
    callback();
  }
  phoxy.AJAX('phone/ExplainValues' + ToGetArr(ids), Callback);
}

function RequestParams(ids, callback)
{
  function Callback(data)
  {
    for (var k in data.data.types)
      phone_info.params[k] = data.data.types[k];
    callback();
  }
  phoxy.AJAX('phone/ExplainTypes' + ToGetArr(ids), Callback);
}