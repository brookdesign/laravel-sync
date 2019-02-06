<?php

namespace Brook\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class brook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brook:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize files from the brook server to your public brook directory.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        $res = $client->request('GET', 'https://brook.test/api/images?api_token='.env('BROOK_KEY'), ['verify' => false]);
        $imageArray = json_decode($res->getBody());
        //dd($imageArray);
        file_put_contents(base_path().'/brook.json', $res->getBody());

        if (!file_exists(public_path().'/brook')) {
            mkdir(public_path().'/brook');
        }

        foreach ($imageArray as $image) {
            foreach ($image->variants as $variant) {
                $imagefile = file_get_contents($variant->variantImageURL);
                file_put_contents(public_path().'/brook/'.$image->id.'-'.$variant->name.'.png', $imagefile);
                $this->comment($image->id.'-'.$variant->name.'.png');
                $this->comment($variant->variantImageURL);
            }
        }
        Artisan::call('view:clear');
        $this->info('You are now synced with the brook server.');
    }
}
