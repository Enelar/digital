function OnDesignBoneLoads()
{
}

function GetElementCode( el )
{
  return $(el).wrapAll('<div></div>').parent().html();
}

function DeferRender( ejs, data )
{
  return phoxy.DeferRender(ejs, data);
}

function MakeModal( modal_selector, obj, ejs, data )
{
  function ActualModalWork()
  {
    phoxy.Appeared(modal_selector, function()
    {
      $(obj)
        .attr('data-toggle', 'modal')
        .attr('data-target', modal_selector)
        .addClass('active');
    });
  }
    
  if (ejs != undefined || $(modal_selector)[0] == undefined)
    phoxy.ApiAnswer({design: ejs, "data" : data}, ActualModalWork);
  else
    ActualModalWork();
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