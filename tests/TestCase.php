<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Routing\UrlGenerator;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use MakesGraphQLRequests;
    use RefreshesSchemaCache;
    // use TestsSubscriptions;

    public function domain(?Tenant $tenant): self
    {
        /**
         * UrlGeneratorのrouteメソッドをモックする
         * partialMockだとUrlGeneratorの他メソッドが呼ばれたとき、なぜかBadMethodCallExceptionが発生する。
         */
        $urlGenerator = app('url');
        $this->app->instance('url', new class($urlGenerator, $tenant) extends UrlGenerator
        {
            protected $tenant;

            public function __construct($url, $tenant)
            {
                parent::__construct($url->routes, $url->request);

                $this->tenant = $tenant;
            }

            public function route($name, $parameters = [], $absolute = true)
            {
                return "http://{$this->tenant->subdomain}.localhost/graphql";
            }
        });

        return $this;
    }
}
