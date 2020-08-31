@extends('layouts.guest')

@section('content')
    <div class="login">
        <div class="login-wrapper">
            <div class="card">
                <h1 class="text-center">パスワードリセット</h1>
                <form action="{{ route('admin.forgot-password') }}" method="post" class="mt-2">
                    @csrf
                    <div class="form-group">
                        <label>メールアドレス</label>
                        <input type="text" class="form-control" name="email" autofocus="autofocus" value="{{ old('email') }}" placeholder="メールアドレスを入力">
                        <p>@include('layouts.components.alert.field', ['field' => 'email'])</p>
                    </div>
                    <div class="mt-4 col text-center">
                        <button type="submit" class="pb-btn-primary">送信</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
