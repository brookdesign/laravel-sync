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
        
        Blade::directive('brookstyle', function ($expression) {
            $imageArray = json_decode(file_get_contents(base_path().'/brook.json'));
            foreach ($imageArray as $image) {
                if ($image->firebase_stream_name === $expression) {
                    $name = $image->milestoneID;
                    break;
                }
            }
            return "<?php echo 'style=\"background: url(/brook/$name.png); background-size: cover;\"' ?>";
        });

        Blade::directive('brookfavicon', function ($expression) {
            $imageArray = json_decode(file_get_contents(base_path().'/brook.json'));
            $output = '';
            foreach ($imageArray as $image) {
                if ($image->firebase_stream_name === $expression) {
                    foreach ($image->milestonePages as $page) {
                        $output .= '<link rel="icon" type="image/png" href="/brook/'.$page->pageImageKey.'.png" sizes="'.$page->pageWidth.'x'.$page->pageHeight.'">';
                    }
                }
            }
            return "<?php echo '$output' ?>";
        });

        Blade::directive('brook', function ($expression) {
            $imageArray = json_decode(file_get_contents(base_path().'/brook.json'));
            $output = '';
            foreach ($imageArray as $image) {
                if ($image->firebase_image_name === $expression) {

                    $output = '<picture>';
                    foreach (array_reverse($image->variants) as $variant) {
                        $output .= '<source media="(min-width: '.$variant->minWidth.'px)" srcset="/brook/'.$image->id.'-'.$variant->name.'.png">';
                    }
                    $output .= '<img src="/brook/'.$image->id.'-'.array_reverse($image->variants)[0]->name.'.png" style="width:auto;">';
                    $output .= '</picture>';
                }
            }
            return "<?php echo '$output' ?>";
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
