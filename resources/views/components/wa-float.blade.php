@props([
    'message' => null,
])

@php
    $waService = app(\App\Services\WhatsAppService::class);
    $waUrl = $waService->getUrl($message);
@endphp

@if($waService->isEnabled() && $waUrl)
    <a href="{{ $waUrl }}"
       class="wa-float"
       target="_blank"
       rel="noopener noreferrer"
       aria-label="Chat WhatsApp Konsultasi Training Defensive Driving">
        <svg viewBox="0 0 32 32" width="28" height="28" fill="#fff" aria-hidden="true">
            <path d="M16 3C9.4 3 4 8.4 4 15c0 2.3.7 4.5 1.8 6.3L4 29l7.9-1.8A12 12 0 0 0 16 27c6.6 0 12-5.4 12-12S22.6 3 16 3zm0 22c-1.9 0-3.7-.5-5.300-1.500l-.4-.2-4.100.9.9-4-.3-.4A9.900 9.900 0 0 1 6 15c0-5.500 4.500-10 10-10s10 4.500 10 10-4.500 10-10 10zm5.500-7.500c-.3-.1-1.800-.9-2.100-1s-.5-.1-.7.1-.8 1-1 1.200-.4.200-.7.100a8.200 8.200 0 0 1-4.100-3.600c-.3-.6.300-.5.900-1.700.1-.2 0-.4 0-.5l-1-2.300c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.6.100-.9.400s-1.100 1.100-1.100 2.600 1.100 3 1.300 3.200 2.200 3.400 5.400 4.700c2 .8 2.800.9 3.800.7.600-.1 1.800-.7 2.100-1.500s.3-1.400.2-1.500-.3-.2-.6-.3z"/>
        </svg>
    </a>

    <style>
    .wa-float {
        position: fixed;
        right: 20px;
        bottom: 20px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #25D366;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .25);
        z-index: 9999;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .wa-float:hover,
    .wa-float:focus-visible {
        transform: scale(1.08);
        box-shadow: 0 6px 18px rgba(0, 0, 0, .35);
        outline: 2px solid #2563EB;
        outline-offset: 2px;
    }
    </style>
@endif
