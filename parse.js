var 
  system = require('system'),
  model, output, size;
var handler = system.args[1];

if (system.args.length < 3)
  phantom.exit();

model = system.args[2];


var page = require('webpage').create();

page.onResourceError = function(resourceError)
{
  page.reason = resourceError.errorString;
  page.reason_url = resourceError.url;
};

phantom.addCookie({
  'name'     : 'yandex_gid',   /* required property */
  'value'    : 2,  /* required property */
  'domain'   : 'market.yandex.ru',
  'path'     : '/',                /* required property */
  'expires'  : (new Date()).getTime() + (1000 * 60 * 60)   /* <-- expires in 1 hour */
});

function ExtractFromPage( url, extract, cb )
{
  page.open(url, function (status) 
  {
    if (status !== 'success')
    {
      console.log('Unable to load the address!');
      console.log(status);
      console.log(page.reason);
      console.log(page.reason_url);
      phantom.exit();
    }
    else 
    {
      console.log(url);
      page.includeJs("http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", 
        function() 
        {
          console.log(page.content.length);
          var res = page.evaluate(extract);
          cb(res);
        });
    }
  });  
}

var handlers = [];
var cur_handler = 0;

var ret;

handlers.push(function()
{
  ExtractFromPage(
    'http://market.yandex.ru/offers.xml?how=aprice&modelid=' + model, 
    function ()
    {
      var res = {};
      res.prices = [];
      $('.b-offers__list .b-old-prices__num')
        .each(function() 
        { 
          res.prices.push($(this).html().replace('&nbsp;', ''))
        });

      res.shops = [];
      $('.shop-link.b-address__link')
        .each(function()
        {
          res.shops.push($(this).html());
        });
        
      return res;
    },
    function (res)
    {
      ret = res;
      Exit();
    });
});

handlers.push(function()
{
  ExtractFromPage(
    'http://market.yandex.ru/model.xml?modelid=' + model, 
    function ()
    {
      var res = {};
      res.avg = $('.price__int').html().replace('&nbsp;', '');

      return res;
    },
    function (res)
    {
      ret = res;
      Exit();
    });
});

handlers.push(function()
{
  ExtractFromPage(
    'http://market.yandex.ru/model-spec.xml?modelid=' + model, 
    function ()
    {
      var res = {};
      res.keys = [];
      
      $('.b-properties th span')
        .each(function() 
        { 
          res.keys.push($(this).html())
        });

      res.values = [];
      $('.b-properties td')
        .each(function()
        {
          res.values.push($(this).html());
        });
        
      return res;
    },
    function (res)
    {
      ret = res;
      Exit();
    });
});

function Exit()
{
  console.log(JSON.stringify(ret));
  phantom.exit();
};

handlers[handler]();
