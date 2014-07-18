var system = require('system'),
    address, output, size;
if (system.args.length < 2)
  phantom.exit();

address = system.args[1];
//console.log(address);
var page = require('webpage').create();
page.settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36';

page.onResourceError = function(resourceError) {
    page.reason = resourceError.errorString;
    page.reason_url = resourceError.url;
};

page.open(address, function (status) 
{
 if (status !== 'success')
 {
//  console.log('Unable to load the address!');
//  console.log(status);
//  console.log( page.reason);
//  console.log(page.reason_url);
  phantom.exit();
 } else 
 {
   window.setTimeout(function () 
   {
page.evaluate(function()
{
 $('body title').appendTo('html > head');
 $('body meta').appendTo('html > head');
 $('script').remove();
});
    console.log(page.content);
//                page.render(output);
    phantom.exit();
   }, 3000);
 }
});

