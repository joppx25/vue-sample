@extends('layouts.guest')

@section('content')
    <div class="login">
        <div class="login-wrapper">
            <div class="card">
                <h1 class="text-center">ログイン</h1>
                <form action="{{ route('admin.login') }}" method="post" class="mt-2">
                    @csrf
                    <div class="form-group">
                        <label>メールアドレス</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email') }}" autofocus="autofocus" placeholder="メールアドレスを入力">
                        <p>@include('layouts.components.alert.field', ['field' => 'email'])</p>
                    </div>
                    <div class="form-group">
                        <label>パスワード</label>
                        <input type="password" class="form-control" name="password" placeholder="パスワードを入力（8文字以上）">
                        <p>@include('layouts.components.alert.field', ['field' => 'password'])</p>
                    </div>
                    <div class="form-group option-box">
                        <label class="checkmark-container">
                            <input type="checkbox" name="remember_me" value="0"> ログイン情報を保存する
                            <span class="checkmark"></span>
                        </label>
                        <label class="float-right">
                            <a href="{{ route('admin.forgot-password') }}">パスワードを忘れた方はこちら</a>
                        </label>
                    </div>
                    <div class="mt-2 col text-center">
                        <button type="submit" class="pb-btn-primary">ログイン</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
