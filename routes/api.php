<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
    $api->group(['namespace' => 'App\\Api\\V1\\Controllers'], function(Router $api) {
        $api->group(['prefix' => 'auth'], function(Router $api) {
            $api->post('signup', 'SignUpController@signUp');
            $api->post('login', 'LoginController@login');

            $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
            $api->post('reset', 'ResetPasswordController@resetPassword');

            $api->post('logout', 'LogoutController@logout');
            $api->post('refresh', 'RefreshController@refresh');
            $api->get('me', 'UserController@me');
        });

        $api->get('hello', 'RandomController@hello');
        $api->get('products', 'ProductController@all');
        $api->get('categories', 'CategoryController@all');

        $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->get('protected', 'RandomController@protected_route');
            $api->get('cart', 'CartController@index');
            $api->post('cart', 'CartController@add_to_cart');
            $api->group(['middleware' => 'jwt.refresh'], function(Router $api) {
                $api->get('refresh', 'RandomController@refresh_route');
            });
        });

    });


});