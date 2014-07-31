var 
  system = require('system'),
  model, output, size;
var handler = system.args[1];

if (system.args.length < 3)
  phantom.exit();

model = system.args[2];


var page = require('webpage').create();
page.settings.resourceTimeout = 15000; // 15 seconds
page.settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36';
page.onResourceTimeout = function(e) {
  console.log('timeout');
  phantom.exit(1);
};

phantom.addCookie({
  'name'     : 'yandex_gid',   /* required property */
  'value'    : 2,  /* required property */
  'domain'   : 'market.yandex.ru',
  'path'     : '/',                /* required property */
  'expires'  : (new Date()).getTime() + (1000 * 60 * 60)   /* <-- expires in 1 hour */
});

var fs = require('fs');
var CookieJar = "/tmp/cookiejar.json";
var pageResponses = {};
page.onResourceReceived = function(response) {
    pageResponses[response.url] = response.status;
    fs.write(CookieJar, JSON.stringify(phantom.cookies), "w");
};
if(fs.isFile(CookieJar))
    Array.prototype.forEach.call(JSON.parse(fs.read(CookieJar)), function(x){
        phantom.addCookie(x);
    });


function ExtractFromPage( url, extract, cb )
{
  rurl = url;
  page.open(url, function (status) 
  {
    if (status !== 'success')
    {
      console.log(status);
      console.log(page.reason);
      console.log(page.reason_url);
      console.log('LOAD_FAIL');
      phantom.exit();
    }
    else 
    {
      //console.log(url);
      page.includeJs("http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js", 
        function() 
        {
          //console.log(page.content.length);
          var res = page.evaluate(extract);
          cb(res);
        });
    }
  });  
}

var handlers = [];
var cur_handler = 0;

var ret;
var rurl;

handlers.push(function()
{
  ExtractFromPage(
    'http://market.yandex.ru/offers.xml?how=aprice&page=2&modelid=' + model,
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
      res.success = $('.b-model-tabs').size();
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
      res.success = $('.b-model-tabs').size();
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
      res.success = $('.b-model-tabs').size();  
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
  ret.body = page.content;
  ret.shot = page.renderBase64('PNG');
  ret.url = rurl;
  console.log(JSON.stringify(ret));
  phantom.exit();
};

handlers[handler]();
