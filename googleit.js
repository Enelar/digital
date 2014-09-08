var system = require('system'),
    address, output, size;
if (system.args.length < 3)
  phantom.exit();

file = system.args[1];
address = system.args[2];
//console.log(address);
var page = require('webpage').create();
page.settings.userAgent = 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36';

page.onResourceError = function(resourceError) {
    page.reason = resourceError.errorString;
    page.reason_url = resourceError.url;
};

page.onConsoleMessage = function(msg) {
    //system.stdout.writeLine('console: ' + msg);
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
  }
  else 
  {
    window.setTimeout(function () 
    {
      page.evaluate(function()
      {
        $('script').remove();
        $('body title').appendTo('html > head');
        $('body meta').appendTo('html > head');
   
        $('.showgoogle').removeClass('showgoogle');
        $('.hidegoogle').remove();
        $('.ejs_ancor').remove(); // workaound bug
      });
      Dump(page.content);
      // page.render(output);
      phantom.exit();
    }, 10000);
  }
});

function Dump( result )
{
  var fs = require('fs');
  fs.write(file, result, 'w');
}
