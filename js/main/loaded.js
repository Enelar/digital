window.onerror = function(msg, url, line) {
  var e = new Error('dummy');
  if (typeof(e.stack) == 'undefined')
    return; 
  var stack = e.stack.replace(/^[^\(]+?[\n$]/gm, '')
      .replace(/^\s+at\s+/gm, '')
      .replace(/^Object.<anonymous>\s*\(/gm, '{anonymous}()@')
      .split('\n');

  var preventErrorAlert = false;
//  window._gaq.push(['_trackEvent', 'JS Error', msg, stack + ' -> (' + url + ") : " + line, 0, true]);
  return preventErrorAlert;
};

requirejs.config({
  baseUrl: 'js'
});

function AJAXImage()
{
  return $('<img />').attr('src', 'res/loading.gif');
}
 
function OnDesignBoneLoads()
{
  /* Плагин для фокси */
  phoxy.plugin = {};
  phoxy.Appeared('#phoxy_ajax_active', function ()
  {
    $('#phoxy_ajax_active').css({'display': 'block'});
    phoxy.plugin.ajax = {};
    
    phoxy.plugin.ajax.nesting_level = 0;
    phoxy.plugin.ajax.active = [];
    phoxy.plugin.ajax.active_id = 0;
    $('#phoxy_ajax_active').stop(true).animate({'opacity': 0});
    
    var phoxy_AJAX = phoxy.AJAX;
    phoxy.AJAX = function(a, cb, params)
    {
      phoxy.plugin.ajax.nesting_level++;
      var my_id = phoxy.plugin.ajax.active_id++;
      phoxy.plugin.ajax.active[my_id] = arguments;
      if (phoxy.plugin.ajax.nesting_level >= 1)
        $('#phoxy_ajax_active').stop(true).animate({'opacity': 1});
      function CallbackHook()
      {
        phoxy.plugin.ajax.nesting_level--;
        delete phoxy.plugin.ajax.active[my_id];
        if (phoxy.plugin.ajax.nesting_level < 0)
          phoxy.plugin.ajax.nesting_level = 0;
        if (phoxy.plugin.ajax.nesting_level == 0)
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
      $('#' + id).html(AJAXImage());
    });
    return res;
  }
   
  var phoxy_ChangeHash = phoxy.ChangeHash;
  phoxy.ChangeHash = function(hash)
  {
    TrackPage(hash);
    return phoxy_ChangeHash.apply(this, arguments);
  }
  TrackPage(location.hash);
  arguments[1]();

  // retailrocket.ru
  var rrPartnerId = "Ваш PartnerId";       var rrApi = {};        var rrApiOnReady = rrApiOnReady || [];       rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view =            rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};       (function(d) {           var ref = d.getElementsByTagName('script')[0];           var apiJs, apiJsId = 'rrApi-jssdk';           if (d.getElementById(apiJsId)) return;           apiJs = d.createElement('script');           apiJs.id = apiJsId;           apiJs.async = true;           apiJs.src = "//cdn.retailrocket.ru/content/javascript/api.js";           ref.parentNode.insertBefore(apiJs, ref);       }(document)); 
  if (location.host.indexOf("demo") != -1)
    analytics.identify({"demo": 1});
}

function TrackPage( hash )
{
  function Unify( url )
  {
    return  url.replace("#!", "#");
  }
  window.analytics.page({ path: Unify(hash), url: Unify(location.href) });


  // Hide google analystics after tracking complete
  if (location.search.indexOf('utm_source') != -1)
    history.pushState({}, document.title, location.pathname + location.hash);
}

function GetElementCode( el )
{
  return $(el).wrapAll('<div></div>').parent().html();
}

function DeferRender( ejs, data )
{
  return phoxy.DeferRender(ejs, data);
}

function MakeModal( modal_selector, obj, ejs, data, callback )
{
  function ActualModalWork()
  {
    phoxy.Appeared(modal_selector, function()
    {
      $(obj)
        .attr('data-toggle', 'modal')
        .attr('data-target', modal_selector)
        .addClass('active_element')
        ;
      if (typeof(callback) == 'function')
        callback(obj, modal_selector);
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

function ToggleProper(trigger, target)
{
  trigger = $(trigger);  
  if (typeof(target) == 'undefined')
    target = trigger;
  target = $(target);
  
  trigger
    .hover
    (
      function()
      {
        target.properShow();
      }
      ,
      function()
      {
        target.properHide();
      }
    );
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
