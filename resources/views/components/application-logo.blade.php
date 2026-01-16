<img
    {{ $attributes->merge(['class' => 'dark:hidden']) }}
    src="{{ asset('images/flow-forms-logo.svg') }}"
    alt="Flow Forms"
/>
<img
    {{ $attributes->merge(['class' => 'hidden dark:block']) }}
    src="{{ asset('images/flow-forms-logo-dark.svg') }}"
    alt="Flow Forms"
/>
