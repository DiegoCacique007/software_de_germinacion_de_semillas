@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-control rounded-3 py-2 px-3']) }}>
