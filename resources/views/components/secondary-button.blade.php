<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary fw-semibold px-4 py-2 rounded-3']) }}>
    {{ $slot }}
</button>
