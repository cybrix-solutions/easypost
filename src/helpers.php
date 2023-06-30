<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Facades\EasyPost;

/**
 * Determine if a production EasyPost API key is set.
 */
function hasApiKey(): bool
{
    return filled(EasyPost::apiKey());
}
