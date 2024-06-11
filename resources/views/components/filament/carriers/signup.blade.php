@props([
    'service',
])

@php
    use Illuminate\Support\HtmlString;
    use Illuminate\Support\Str;

    use function CybrixSolutions\EasyPost\setLinkTargets;

    $signupHelpUrl = $service->signupHelpUrl()
        ? new HtmlString(
            setLinkTargets(Str::markdown(__('easypost::labels.custom_workflow_help', ['url' => $service->signupHelpUrl()])))
        )
        : null;
@endphp

<div {{ $attributes->class('space-y-2') }}>
    @if ($service->signupUrl())
        <a
            href="{{ $service->signupUrl() }}"
            target="_blank"
            rel="noopener nofollow external"
        >
            {{ $service->signupText() }}
        </a>
    @elseif ($service->signupInstructions())
        {{ new HtmlString(Str::markdown($service->signupInstructions())) }}
    @endif

    @if ($signupHelpUrl)
        {{ $signupHelpUrl }}
    @endif
</div>
