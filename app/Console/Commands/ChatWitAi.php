<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

use function Laravel\Prompts\textarea;

class ChatWitAi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $message = textarea("message");
        // dd($message);
        dd($this->prismFactory("hello there"));

    }

    protected function prismFactory($message)
    {
        $response  = Prism::text()
            ->using(Provider::OpenAI, 'google/gemma-3-12b-it')
            // ->withSystemPrompt('vous êtess un expert en laravel 11.x')
            ->withPrompt("que ce que le CNN? ")
            ->asText();
        dd($response->text);
    }


}
