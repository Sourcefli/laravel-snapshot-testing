<?php

namespace Sourcefli\SnapshotTesting\Commands;

use Illuminate\Console\Command;

class SnapshotTestingCommand extends Command
{
    public $signature = 'laravel-snapshot-testing';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
