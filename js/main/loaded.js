  
function OnDesignBoneLoads()
{
  phoxy.Appeared('#phoxy_ajax_active', function ()
  {
    $('#phoxy_ajax_active').css({'display': 'block'});
    phoxy.plugin_ajax_nesting_level = 0;
    var phoxy_AJAX = phoxy.AJAX;
    phoxy.AJAX = function(a, cb, params)
    {
      phoxy.plugin_ajax_nesting_level++;
      if (phoxy.plugin_ajax_nesting_level >= 1)
        $('#phoxy_ajax_active').stop(true).animate({'opacity': 1});
      function CallbackHook()
      {
        phoxy.plugin_ajax_nesting_level--;
        if (phoxy.plugin_ajax_nesting_level < 0)
          phoxy.plugin_ajax_nesting_level = 0;
        if (phoxy.plugin_ajax_nesting_level == 0)
          $('#phoxy_ajax_active').stop(true).animate({'opacity': 0});
        cb.apply(this, arguments);
      }
      return phoxy_AJAX(a, CallbackHook, params);
    }
  });
  var phoxy_DeferRender = phoxy.DeferRender;
  phoxy.DeferRender = function()
  {
    var res = phoxy_DeferRender.apply(this, arguments);
    var id = $(res).attr('id');
    phoxy.Defer(function () {
      $('#' + id).html($('<img />').attr('src', 'res/loading.gif'));
    });
    return res;
  }

  function GoogleAnalysticsEvent()
  {
    try
    {
      _gaq.push(['_trackPageview',location.pathname + location.search  + location.hash]);
    } catch (e)
    {
    }  
  }

  GoogleAnalysticsEvent();
  $(window).bind('hashchange', function()
  {
    GoogleAnalysticsEvent();
  });
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

$.fn.properHide = function ()
{
  $this = this;
  var i, len, $result = jQuery([]);
  len = $this.length;
  for (i=0; i<len; i++) {
    $currentElem = $this.eq(i);
    $currentElem
      .animate(
      {
        opacity: 0
      },
      500,
      function()
      {
        $currentElem.css({visibility: 'hidden'})
      });
      
    $result.pushStack($currentElem);
  }

  return this;
}

$.fn.properShow = function ()
{ 
  $this = this;
  var i, len, $result = jQuery([]);
  len = $this.length;
  for (i=0; i<len; i++) {
    $currentElem = $this.eq(i);
    $currentElem
      .finish()
      .css({visibility: 'visible'})
      .animate(
      {
        opacity: 1
      });
      
    $result.pushStack($currentElem);
  }

  return this;
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