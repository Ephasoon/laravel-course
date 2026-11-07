@props(['title' => '', 'bodyClass' => ''])
<x-base-layout :title="$title" :bodyClass="$bodyClass">
  <x-layouts.header class="header"></x-layouts.header>
  <x-flash-message />
  {{ $slot }}
</x-base-layout>
