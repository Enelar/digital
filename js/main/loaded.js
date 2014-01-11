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

function MakeModal( modal_selector, obj )
{
  $(obj)
    .attr('data-toggle', 'modal')
    .attr('data-target', modal_selector)
    .addClass('active');
  return obj;
}

function InsertParent( me, parent )
{
  parent = $(parent);
  parent.insertBefore(me);
  
  me = $(me).detach();  
  parent.append(me); 
}

function CorrectPhonesURL()
{
  $('[data-mark="phone"]').each(function()
  {
    var html = $(this).html();
    var num = html.replace(/[\D]+/g, '');
    $(this)
      .attr('href', 'tel:' + num)
      .removeAttr('data-mark')
      .addClass('hipster');
  });
}


$.fn.replaceTag = function (newTagObj, keepProps)
{ // http://stackoverflow.com/questions/918792/use-jquery-to-change-an-html-tag/20469901#20469901
    $this = this;
    var i, len, $result = jQuery([]), $newTagObj = $(newTagObj);
    len = $this.length;
    for (i=0; i<len; i++) {
        $currentElem = $this.eq(i);
        currentElem = $currentElem[0];
        $newTag = $newTagObj.clone();
        if (keepProps) {//{{{
            newTag = $newTag[0];
            newTag.className = currentElem.className;
            $.extend(newTag.classList, currentElem.classList);
            $.extend(newTag.attributes, currentElem.attributes);
        }//}}}
        $newTag.html(currentElem.innerHTML).replaceAll($currentElem);
        $result.pushStack($newTag);
    }

    return this;
}