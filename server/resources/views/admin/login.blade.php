@extends('layouts.app')
 
@section('css')
<link rel="stylesheet" href="{{ mix('css/admin.login.css') }}">
@endsection
@section('content')
<div class="container login-top">
  <div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
      <div class="form-signin">
          <h2 class="form-signin-heading">『明朝互动』</h2>
          <input class="form-control" placeholder="请输入您的账号" id="username" type="text"/>
          <input class="form-control" placeholder="请输入密码" id="password" type="password"/>
          <span id="show-code">
            <input type="text" class="captachcontrol" id="captach" placeholder="请输入验证码">
            <img class="captach" src="{{url('/captach')}}" alt="{{url('/captach')}}" title="看不清，点击刷新">
          </span>
          <div class="form-group">
            <div class="checkbox">
              <label>
                <input type="checkbox" id="remember" checked> 记住密码
              </label>
            </div>
          </div>
          <button class="btn btn-lg btn-primary btn-block" id="submit">登录</button>
      </div>
    </div>
    <div class="col-md-4"></div>
  </div>
</div>
<div aria-hidden="true" class="modal fade bs-example-modal-sm"  id="warning-modal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
          <h5 class="modal-title" >温馨提示！</h5>
      </div>
      <div class="modal-body" id="warning-tip">
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="{{ mix('js/rsa.js') }}"></script>
<script type="text/javascript" src="{{ mix('js/admin.login.js') }}"></script>
@endsection