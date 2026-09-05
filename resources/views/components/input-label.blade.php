@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-bold small text-secondary']) }}>
    {{ $value ?? $slot }}
</label>
