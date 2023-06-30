<div {{ $attributes->class('space-y-2') }}>
    @if ($service->signupUrl())
        <a href="{{ $service->signupUrl() }}"
           target="_blank"
           rel="nofollow noopener external"
        >
            {{ $service->signupText() }}
        </a>
    @elseif ($service->signupInstructions())
        {!! Str::markdown($service->signupInstructions()) !!}
    @endif

    @if ($service->signupHelpUrl())
        {!! Str::markdown(__('easypost::labels.custom_workflow_help', ['url' => $service->signupHelpUrl()])) !!}
    @endif
</div>
