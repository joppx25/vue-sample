@extends('layouts.guest')

@section('content')
    <div class="login">
        <div class="login-wrapper">
            <div class="card">
                <div class="header text-center">
                    <h1>ありがとうございます</h1>
                </div>
                <div class="body-content">
                    <p>パスワードはすでに設定されており、アカウントはすでに検証されています。</p>
                    <p>
                        この<a href="{{ route('admin.login') }}">ログイン</a>リンクをクリックして、管理者ログインにリダイレクトします。
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

