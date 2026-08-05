<?php

namespace Xitara\ERecht24\Console;

use Log;
use Winter\Storm\Console\Command;
use Xitara\ERecht24\Classes\ApiClient;
use Xitara\ERecht24\Models\Settings;

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
    public function handle() : void
    {
        $langs = (array) Settings::get('langs', []);
        $docs = (array) Settings::get('docs', []);

        Log::debug($langs);
        Log::debug($docs);

        if ($langs === [] || $docs === []) {
            Log::warning('No eRecht24 documents or languages configured.');

            return;
        }

        foreach ($docs as $doc) {
            Log::debug($doc);

            $result = (new ApiClient())->importDocument($doc, $langs, 'pull');

            Log::debug($result);
        }
    }
}
