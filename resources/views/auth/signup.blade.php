<x-guest-layout title="Sign up" bodyClass="page-signup">
   <h1 class="auth-page-title">Signup</h1>
            <form action="{{ route('signup') }}" method="POST">
              @csrf
              <div class="form-group @error('name') has-error @enderror">
                <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" />
                @error('name')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group @error('email') has-error @enderror">
                <input type="email" name="email" placeholder="Your Email" value="{{ old('email') }}" />
                @error('email')
                  <div class="error-message">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group @error('phone') has-error @enderror">
                <input type="text" name="phone" placeholder="Phone" value="{{ old('phone') }}" />
                @error('phone')
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
                <input type="password" name="password_confirmation" placeholder="Repeat Password" />
              </div>
              <button class="btn btn-primary btn-login w-full">Register</button>
            </form> 
            <x-slot:footerlink>
              Already have an account? -
                <a href="{{ route('login') }}"> Click here to login </a>
            </x-slot:footerlink>
</x-guest-layout>
