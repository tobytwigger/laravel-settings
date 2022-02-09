<?php

namespace Settings\Http\Middleware;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Settings\Loading\LoadedSettings;

class ShareSettingsWithJs
{

    private LoadedSettings $loadedSettings;
    private Repository $config;

    public function __construct(Repository $config, LoadedSettings $loadedSettings)
    {
        $this->loadedSettings = $loadedSettings;
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        $settings = $this->config->get('laravel-settings.js.autoload', []);

        if(!empty($settings)) {
            $this->loadedSettings->loadMany($settings);
        }

        return $next($request);
    }

}
