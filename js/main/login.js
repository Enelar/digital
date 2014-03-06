function SwitchToReg()
{
  var root = $("#modal-login");
  var form = root.find("[data-mark='login-form']");
  var attr = form.attr("data-switch");
  if (attr == 'reg')
    return;
  form.attr('data-switch', 'reg');
  
  var reg_btn = root.find("[data-mark='switch-to-reg']");
  var login_btn = root.find("[data-mark='login-button']");
  
  var html = reg_btn.html();
  reg_btn.html(login_btn.html());
  login_btn.html(html);
  
  reg_fields = root
    .find("[data-mark='additional-reg-fields']");
  if (reg_fields.html() == '')
  {
    reg_btn.button('loading');
    root.find('button').prop('disabled', true);
    reg_fields
      .slideUp()
      .html
      (
        phoxy.DeferRender(
          'reg/additional_to_login',
          {},
          function ()
          {
            reg_btn.button('reset');
            root.find('button').prop('disabled', false);
            reg_fields.slideDown();
          })
      );
  }
  else
    reg_fields.slideDown();
  
  reg_btn
    .off('click')
    .click(SwitchToLogin);
    
  $('#login-title').html('Регистрация');
}

function SwitchToLogin()
{
  var root = $("#modal-login");
  var form = root.find("[data-mark='login-form']");
  var attr = form.attr("data-switch");
  if (attr == 'login')
    return;
  form.attr('data-switch', 'login');
    
  var reg_btn = root.find("[data-mark='switch-to-reg']");
  var login_btn = root.find("[data-mark='login-button']");
  
  var html = reg_btn.html();
  reg_btn.html(login_btn.html());
  login_btn.html(html);
  
  root.find("[data-mark='additional-reg-fields']").slideUp();

  reg_btn
    .off('click')
    .click(SwitchToReg);

  $('#login-title').html('Авторизация');
}