function SwitchToReg()
{
  var root = $("#modal-login");
  var form = root.find("[data-mark='login-form']");
  var attr = form.attr("data-switch");
  if (attr == 'reg')
    return;
  form.attr('data-switch', 'reg');
  
  var reg_btn = root.find("[data-mark='switch-to-reg']");
  var login_btn = root.find("[data-mark='action-button']");
  
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
  var login_btn = root.find("[data-mark='action-button']");
  
  var html = reg_btn.html();
  reg_btn.html(login_btn.html());
  login_btn.html(html);
  
  root.find("[data-mark='additional-reg-fields']").slideUp();

  reg_btn
    .off('click')
    .click(SwitchToReg);

  $('#login-title').html('Авторизация');
}

function SubmitLoginForm(form)
{

}

function CheckLoginForm()
{
  console.log("checking form", this);
  return true;
}

function GenerateLoginVoice(json)
{
  var ask = json.data.voice.ask;
  var answer = VoiceAnswer(ask, json.data.voice.answer);
  
  var form = $("#modal-login").find("form");
  
  form.find("[name='voice_ask']").remove();
  form.find("[name='voice_answer']").remove();
  form.append($('<input />').attr('type', 'hidden').attr('name', 'voice_ask').attr('value', ask));
  form.append($('<input />').attr('type', 'hidden').attr('name', 'voice_answer').attr('value', answer));
  
  $("#modal-login")
    .find("[data-mark='action-button']")
    .trigger('query');
}

function VoiceAnswer(ask, max_answer)
{
  var c = 0;

  while (true)
  {
    var hex = MD5(ask + c);
    var numb = parseInt(hex, 16);
    if (numb < max_answer)
      return c;
    c++;
  }
}

function ShowLoginError( message )
{
  $('#login-error-place').html($('<code />').html(message.data.error));
  console.log(message.data.error);
}

function RegistrationSuccessfull( data, cb )
{
  $('#login-title').parent()
    .animate({opacity: 0}, 500, function()
    {
      $(this)
        .html('Регистрация успешна. <b>Пожалуйста подтвердите почту.</b>')
        .animate({opacity: 1, color: 'blue'});
    });
    
  if (data.data.reset)
  {
    $('#login-title').append(' Сейчас произойдет перенаправление.');
    phoxy.Defer(function()
    {
      cb.apply(this, data);
    }, 2000);
  }
}

function LoginSoftReset()
{
  phoxy.ApiAnswer({reset: location.hash});
}

