@if (session('success'))
    <div class="success-message">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="flash-error">{{ session('error') }}</div>
@endif
