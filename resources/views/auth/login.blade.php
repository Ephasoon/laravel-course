<x-guest-layout title="Login" bodyClass="page-login">

<h1 class="auth-page-title">Login</h1>
<form action="{{ route('login') }}" method="POST">
              @csrf
              <div class="form-group @error('email') has-error @enderror">
                <input type="email" name="email" placeholder="Your Email" value="{{ old('email') }}" />
                @error('email')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group @error('password') has-error @enderror">
                <input type="password" name="password" placeholder="Your Password" />
                @error('password')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <label>
                  <input type="checkbox" name="remember" value="1" @checked(old('remember')) />
                  Remember me
                </label>
              </div>
              <div class="text-right mb-medium">
                <a href="{{ route('password.request') }}" class="auth-page-password-reset"
                  >Reset Password</a
                >
              </div>

              <button class="btn btn-primary btn-login w-full">Login</button>
                

              
</form>
<x-slot:footerlink>
   Don't have an account? -
   <a href="{{ route('signup') }}"> Click here to create one</a>
</x-slot:footerlink>
</x-guest-layout>
