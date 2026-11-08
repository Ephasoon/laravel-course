<x-guest-layout title="Forgot Password" bodyClass="page-login">

<h1 class="auth-page-title">Forgot Password</h1>
<form action="{{ route('password.email') }}" method="POST">
              @csrf
              <div class="form-group @error('email') has-error @enderror">
                <input type="email" name="email" placeholder="Your Email" value="{{ old('email') }}" />
                @error('email')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>

              <button class="btn btn-primary btn-login w-full">Send Reset Link</button>
</form>
<x-slot:footerlink>
   Remembered your password? -
   <a href="{{ route('login') }}"> Click here to login</a>
</x-slot:footerlink>
</x-guest-layout>
