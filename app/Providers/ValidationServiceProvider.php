<?php

namespace App\Providers;

use App\Validation\Validator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot()
    {
        $this->app->make('validator')
            ->resolver(function ($translator, $data, $rules, $messages, $attributes) {
                return new Validator($translator, $data, $rules, $messages, $attributes);
            });
    }
}
