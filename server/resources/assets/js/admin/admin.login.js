window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');
md5 = require('js-md5');

$("#submit").click(login);
keyBody();
if(window.localStorage && localStorage.username && localStorage.password){
  $("#username").val(localStorage.username);
  $("#password").val(localStorage.password);
}
$("#show-code").hide();
$('.captach').on('click',function(){
  setCaptach($(this));
});

function showCode(errcount) {
  if(errcount > 3) {
    $("#show-code").show();
  } else {
    $("#show-code").hide();
  }
}

function keyBody() {
  $("body").on("keydown", function(event) {
    if(event.keyCode == "13") {
    var data = getValue();
      $("#warning-modal").modal("hide");
      if(data.username !== "" && data.password !== "") {
        login();
        return false;
      }
    }
  });
}

function login() {
  var data = getValue();
  if( data.username === '' ) {
    $("#warning-tip").html("请输入用户名");
    $("#warning-modal").modal("show");
    return false;
  }

  if( data.password === '' ) {
    $("#warning-tip").html("请输入密码");
    $("#warning-modal").modal("show");
    return false;
  }

  var rsa_n = "D1F0795CC5A8D0BE60B83FF555F055CE7A44A0EC17BE365587A39696F76D4067BAE06D0026836E873CDAFC5409F1D53C6C931E6819D9B126516A5320CC30DB1B055B1AD9A2764814F8075659455A320006FD262F72B9AFE7B2778F2D20405CC9510BCE96137D56749BE169475DA6A6FAE4D955775988DFBE47A5E4B4E8494DD9C8658CE8A066D02B9C8165CD6E3C3CC803CA810B62529611A5AB87B9475322D490F9E47A1941BD8CEE395AACDC4A2E55964A03F7C575C036B79CD82490D404AE00F315A0700B8A1FE15C358FD071CDFB29761B78600ACD240DAFEB8516E1C8FF42C38C8E732DAC293FA202444FD218D5DC5DA5308D73363FCFD2EC1CF6D716EF";
  setMaxDigits(259); //259 => n的十六进制位数/2+3
  var key = new RSAKeyPair("10001", '', rsa_n); //10001 => e的十六进制
  data.password = md5(data.password);
  data.password = encryptedString(key, data.password); //不支持汉字
  ajaxPost(data,"/admin/doLogin",loginFn);
}

//登陆成功的函数
function loginFn(json) {
  if( json.ret === 0 ) {
    var data = getValue();
    if( $("#remember")[0].checked === true ) {
      save(data.username,data.password);
    } else {
      save("","");
    }
    window.location.href = json.data.jumpUrl;
  } else {
    showCode(json.data.errcount);
    setCaptach($('.captach'));
    $("#captach").val('');
    $("#password").val('');
    showModelTips(json.msg);
  }
}

//记住密码和用户名
function save(username,password) {
  if(window.localStorage) {
    localStorage.username = username;
    localStorage.password = password;
  }
}

function getValue() {
  var data = {
    username: $("#username").val(),
    password: $("#password").val(),
    captach: $('#captach').val()
  };
  return data;
}

function showModelTips(msg) {
  $("#warning-tip").html( msg );
  $("#warning-modal").modal("show");
}

function ajaxPost(data,url,successfn) {
  showModelTips('登录中...');
  $.ajax({
    headers:{
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: "POST",
    url: url,
    data: data,
    dataType:"json",
    success: function (json) {
      successfn(json);
    },
    error: function (request) {
    }
  });
}

//获取cookie
function getCookie(cname) {
  var name = cname + "=";
  var ca = document.cookie.split(';');
  for(var i=0; i<ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1);
    if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
  }
  return "";
}

function setCaptach(obj){
  obj[0].src = obj.attr('alt')+'?'+Math.random();
}
