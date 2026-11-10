@props(['name' => 'form-modal', 'show' => false, 'maxWidth' => '2xl'])
@php
$width = ['sm'=>'sm:max-w-sm','md'=>'sm:max-w-md','lg'=>'sm:max-w-lg','xl'=>'sm:max-w-xl','2xl'=>'sm:max-w-2xl','4xl'=>'sm:max-w-4xl'][$maxWidth] ?? 'sm:max-w-2xl';
@endphp
<div
    x-data="{ show: @js($show) }"
    x-init="$watch('$wire.form', value => show = value)"
    x-on:keydown.escape.window="show = false; $wire.set('form', false)"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6"
    style="display: none"
>
    <div x-on:click="show = false; $wire.set('form', false)" class="fixed inset-0 bg-[#111827]/60 backdrop-blur-sm"></div>
    <div class="relative flex min-h-full items-start justify-center pt-8 sm:items-center sm:pt-0">
        <div x-show="show" x-transition class="relative w-full {{ $width }} overflow-hidden rounded-2xl border border-border bg-surface shadow-2xl">
            {{ $slot }}
        </div>
    </div>
</div>
