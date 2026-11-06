@extends('layout')
@section('content')
<section class="panel auth-panel"><div class="panel-head"><h2>{{ $mode==='register'?'Buat akun':'Masuk ke ruang kerja' }}</h2><span class="pill">AKSES TERLINDUNGI</span></div><form class="panel-body stack" method="post" action="{{ $mode==='register'?'/register':'/login' }}">@csrf
@if($mode==='register')<label>Nama<input name="name" required maxlength="100" autocomplete="name" value="{{ old('name') }}"></label>@endif
<label>Email<input name="email" type="email" required autocomplete="username" value="{{ old('email') }}"></label>
<label>Password<input name="password" type="password" required autocomplete="{{ $mode==='register'?'new-password':'current-password' }}"></label>
@if($mode==='register')<label>Konfirmasi password<input name="password_confirmation" type="password" required autocomplete="new-password"></label><small>Gunakan sedikitnya 10 karakter.</small>@endif
<button class="primary">{{ $mode==='register'?'Buat akun':'Masuk' }}</button>
@if($mode==='register')<a href="/login">Sudah memiliki akun? Masuk</a>@elseif($can_register)<a href="/register">Buat akun baru</a>@else<small>Pendaftaran pemilik sudah ditutup.</small>@endif
</form></section>
@endsection
