function OnDesignBoneLoads()
{
}

function GetElementCode( el )
{
  return $(el).wrapAll('<div></div>').parent().html();
}

// I know that its crap code, but we hurring
function DeferRender( ejs, data )
{
  function GenerateIniqueID()
  {
    var ret = "";
    var dictonary = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 10; i++)
      ret += dictonary.charAt(Math.floor(Math.random() * dictonary.length));

    return ret;
  }

  var id = GenerateIniqueID();
  var div = GetElementCode($('<div/>').attr('id', id).attr("data-debug_comment", "Staged for defer loading. Will be anigilated soon."));

  var replace_callback = function()
  {
    $("#" + id).replaceWith($("#" + id).html());
  };

  var func;
  
  if (typeof(data) == 'undefined')
  { // single param call
    if (typeof(ejs) == 'object')
    { // called as constructed object
      func = function()
      {
        ejs.result = id;
        phoxy.ApiAnswer(ejs, replace_callback);
      };
    }
    else
    { // called as phoxy rpc
      func = function()
      {
        phoxy.ApiRequest(ejs, replace_callback);
      };
    }
  }
  else
  { // called as design submodule (only ejs string and that data)
    func = function()
    {
      phoxy.ApiAnswer({design : ejs, "data" : data, result : id}, replace_callback);
    };
  }
  
  setTimeout(func, 0);
  return div;
}
