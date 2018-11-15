<?php

namespace Brook;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BrookServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Brook\Commands\brook',
    ];
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('brook', function ($expression) {
            $imageArray = json_decode(file_get_contents(base_path().'/brook.json'));
            foreach ($imageArray as $image) {
                if ($image->firebase_name === $expression) {
                    $name = $image->milestoneID;
                    break;
                }
            }
            return "<?php echo '<img width=\"50%\" src=\"/brook/$name.png\" />' ?>";
        });

        Blade::directive('brookstyle', function ($expression) {
            $imageArray = json_decode(file_get_contents(base_path().'/brook.json'));
            foreach ($imageArray as $image) {
                if ($image->firebase_name === $expression) {
                    $name = $image->milestoneID;
                    break;
                }
            }
            return "<?php echo 'style=\"background: url(/brook/$name.png); background-size: cover;\"' ?>";
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}
