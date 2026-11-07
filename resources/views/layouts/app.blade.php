@props(['title' => ''])
<x-base-layout :title="$title">
  <x-layouts.header class="header"></x-layouts.header>
      {{ $slot }}
      @hasSection('footerLinks')
      @endif
</x-base-layout>    

 
