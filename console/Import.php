<?php

namespace Xitara\ERecht24\Console;

use Winter\Storm\Console\Command;
use Xitara\ERecht24\Models\Settings;
use Xitara\ERecht24\Classes\ApiClient;
use Log;

class Import extends Command
{
    /**
     * @var string The console command name.
     */
    protected static $defaultName = 'erecht:import';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'erecht:import';

    /**
     * @var string The console command description.
     */
    protected $description = 'Fetch selected docs in selected languages from eRecht24 API';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $langs = Settings::get('langs', null);
        $docs = Settings::get('docs', null);

        Log::debug($langs);
        Log::debug($docs);

        foreach ($langs as $lang) {
            foreach ($docs as $doc) {
                Log::debug($lang);
                Log::debug($doc);

                $result = ApiClient::{$doc}($lang);

                Log::debug($result);
            }
        }
    }
}
