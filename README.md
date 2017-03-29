#WordPress Http
##Easy RESTful routing in WordPress inspired by Laravel.
**Features**
* Middleware
* Named Routes
* Route Parameters
* Closure and Controller support

###routes/web.routes.php
```php
use Webcode\WP\Http\RouteService as Route;

Route::get('/', function () {
    Route::dump_routes();
})->name('home');
```

###TestController.php
```php
namespace Webcode\WP\Http\Controllers;

use Webcode\WP\Http\Controller;

class TestController extends Controller
{
    public function test()
    {
        $this->response->headers->set('Content-Type', 'text/plain');
        $this->response->setContent('Hello ' . $this->request->query->get('name'));
        $this->response->send();
    }
}
```

###routes/api.routes.php
```php
use Webcode\WP\Http\RouteService as Route;
use Webcode\WP\Http\Request;
use Webcode\WP\Http\Response;

Route::group(['name' => 'auth:', 'prefix' => 'auth'], function () {
    Route::post('sign-in', 'AuthController@signIn')->name('login');

    Route::post('sign-out', function (Request $request, Response $response) {
        wp_logout();
    })->name('logout');
});
```

###index.php
```php
include "vendor/autoload.php";
use Webcode\WP\Http\Exceptions\NotFoundHttpException;
use Webcode\WP\Http\RouteService as Router;

add_action('do_parse_request', function($do_wp_parse) {
    try {
        Router::get_instance('YourVendor\\YourPlugin\\Http\\Controllers\\', []);
        Router::group(['name' => 'api:', 'prefix' => 'api'], function () {
            include "routes/api.routes.php";
        });
        Router::group(['name' => 'web:'], function () {
            include "routes/web.routes.php";
        });
        Router::run();
    } catch (NotFoundHttpException $ex) {
        //TODO: Log this $ex->getMessage();
        return $do_wp_parse;
    }
}, 30, 2);
```