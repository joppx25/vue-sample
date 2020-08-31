@extends('layouts.guest')

@section('content')
    <div class="login">
        <div class="login-wrapper">
            <div class="card">
                <h1 class="text-center">パスワードを変更する</h1>
                <form action="{{ route('admin.change-password.submit') }}" method="post" class="mt-2">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="form-group">
                        <label>パスワード</label>
                        <input type="password" class="form-control" name="password" placeholder="パスワードを入力（8文字以上）">
                        <p>@include('layouts.components.alert.field', ['field' => 'password'])</p>
                    </div>
                    <div class="form-group">
                        <label>パスワードの確認</label>
                        <input type="password" class="form-control" name="confirm_password" placeholder="パスワードを入力（8文字以上）">
                        <p>@include('layouts.components.alert.field', ['field' => 'confirm_password'])</p>
                    </div>
                    <div class="mt-4 col text-center">
                        <button type="submit" class="pb-btn-primary">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

