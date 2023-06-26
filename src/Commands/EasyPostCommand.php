<?php

namespace CybrixSolutions\EasyPost\Commands;

use Illuminate\Console\Command;

class EasyPostCommand extends Command
{
    public $signature = 'easypost';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
