<x-guest-layout title="Reset Password" bodyClass="page-login">

<h1 class="auth-page-title">Reset Password</h1>
<form action="{{ route('password.update') }}" method="POST">
              @csrf
              <input type="hidden" name="token" value="{{ $token }}" />
              <div class="form-group @error('email') has-error @enderror">
                <input type="email" name="email" placeholder="Your Email" value="{{ old('email', $email) }}" />
                @error('email')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group @error('password') has-error @enderror">
                <input type="password" name="password" placeholder="New Password" />
                @error('password')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <input type="password" name="password_confirmation" placeholder="Repeat Password" />
              </div>

              <button class="btn btn-primary btn-login w-full">Reset Password</button>
</form>
<x-slot:footerlink>
   Back to -
   <a href="{{ route('login') }}"> Login</a>
</x-slot:footerlink>
</x-guest-layout>
