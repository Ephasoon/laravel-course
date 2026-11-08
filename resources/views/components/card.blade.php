@props(['color', 'bgColor'])
<div class="card card-text-{{ $color }} bg-{{ $bgColor }}">
    <div class="card-header">{{ $title}}</div>
{{ $slot }}
<div class="card-footer">{{ $footer}}</div>
</div>