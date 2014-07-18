var system = require('system'),
    address, output, size;
if (system.args.length < 2)
  phantom.exit();

address = system.args[1];
console.log(address);
var page = require('webpage').create();
page.settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36';

page.onResourceError = function(resourceError) {
    page.reason = resourceError.errorString;
    page.reason_url = resourceError.url;
};

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

page.open(address, function (status) 
{
 if (status !== 'success')
 {
  console.log('Unable to load the address!');
  phantom.exit();
 } else 
 {
   window.setTimeout(function () 
   {
     console.log(page.content);
     phantom.exit();
   }, 3000);
 }
});

