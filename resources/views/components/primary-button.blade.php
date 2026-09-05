<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary fw-semibold px-4 py-2 rounded-3']) }}>
    {{ $slot }}
</button>
