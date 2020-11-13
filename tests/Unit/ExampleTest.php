<?php

namespace Tests\Unit;

use App\Repositories\ConfigRepositoryEloquent;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest ()
    {
        $response = $this->withSession (['login_admin_uid' => 1])->json ('put', 
            route ('configs.update', 1), 
            ['Configs' => ['context' => '232321', 'desc' => '']]
        );

        $response
            ->assertJson ([
                'created' => true,
            ]);

        //$this->assertTrue(true);
    }

    public function testSaveEnv ()
    {
        //$app = new Container();
        //$app->
        //$C = $app->make (ConfigRepositoryEloquent::class);
        //$this->app->make ()
        //$C = app(ConfigRepositoryEloquent::class);
        $C = $this->app->make (ConfigRepositoryEloquent::class);
        //$key = ''
        $C->saveToEnv ('TEST_KEY', '123456123456');
        //$app = app(ConfigRepositoryEloquent::class);
        //
        //$app->boot ()
        //$app->saveToEnv();
        $this->assertTrue(true);
    }
}
