@props(['title' => '', 'bodyClass' => ''])
<x-base-layout :title="$title" :bodyClass="$bodyClass">
  <x-layouts.header class="header"></x-layouts.header>
      {{ $slot }}
      @hasSection('footerLinks')
      @endif
</x-base-layout>    
