<?php

use Orchestra\Testbench\TestCase;
use Luminark\Url\Models\Url;

/**
 * Class UrlTest
 */
class UrlTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        copy(
            __DIR__ . '/../database/migrations/2015_04_12_000000_create_urls_table.php',
            __DIR__ . '/../tests/database/migrations/2015_04_12_000000_create_urls_table.php'
        );
        $this->artisan('migrate', [
          '--database' => 'testbench',
          '--path' => '../tests/database/migrations',
        ]);
        
        // Workaround to get Eloquent Model events working in tests
        Url::flushEventListeners();
        Url::boot();
    }
    
    public function tearDown()
    {
        $this->artisan('migrate:rollback', [
          '--database' => 'testbench'
        ]);
    }
    
    /**
     * Define environment setup.
     *
     * @param Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__ . '/../src';
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
          'driver' => 'sqlite',
          'database' => ':memory:',
          'prefix' => '',
        ]);
        $app['router']->get('{uri?}', ['uses' => '\TestController@getUrlResource'])->where('uri', '.*');
    }
    
    /**
     * Get Luminark Url package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return ['Luminark\Url\UrlServiceProvider'];
    }
    
    public function testResourceUrlUpdate()
    {
        $resource = Resource::create([
            'title' => 'URL testing',
            'uri' => 'url-testing'
        ]);

        $this->assertEquals($resource->uri, 'url-testing', 'Url not properly set.');
        $this->assertNotNull($resource->url, 'Url not properly set.');

        $resource->uri = 'new-url-testing';
        $resource->save();

        $oldUrl = Url::find('url-testing');

        $this->assertEquals('new-url-testing', $resource->uri, 'Url not properly updated.');
        $this->assertNotNull($resource->url, 'New Url is not properly set.');
        $this->assertNotNull($oldUrl->redirectsTo, 'Old Url does not redirect properly.');
        $this->assertEquals($resource->url->id, $oldUrl->redirectsTo->id, 'Url does not redirect properly.');
        
        $resource->uri = null;
        $resource->save();
        
        $this->assertNull($resource->url, 'Url not properly dissociated from resource.');
        
        $this->setExpectedException('Exception');
        $oldUrl->uri = 'new-url';
    }
    
    public function testToString()
    {
        $resource = Resource::create([
            'title' => 'URL testing',
            'uri' => 'url-testing'
        ]);
        
        $this->assertEquals('/url-testing', (string) $resource->url, 'Url not properly converted to string');
    }
    
    public function testResourceUrlDelete()
    {
        $resource = Resource::create([
            'title' => 'Test Deleting',
            'uri' => 'test/deleting-1'
        ]);
        
        $resource->uri = 'test/deleting-2';
        $resource->save();
        
        $resource->uri = 'test/deleting-3';
        $resource->save();
        
        $resource->delete();
        
        $url3 = Url::find('test/deleting-3');
        $this->assertNull($url3, 'Url has not been properly deleted.');
        $url2 = Url::find('test/deleting-2');
        $this->assertNull($url2, 'Old Url has not been properly deleted.');
        $url1 = Url::find('test/deleting-1');
        $this->assertNull($url1, 'Old Url has not been properly deleted.');
    }
    
    public function testUrlVisit()
    {
        $uri = 'foo/bar/url-testing';
        $newUri = 'foo/new-url-testing';
        $title = 'URL testing';
        
        $resource = Resource::create([
            'title' => $title,
            'uri' => $uri
        ]);
        
        $response = $this->call('GET', '/' . $uri);
        $this->assertEquals($title, $response->getContent(), 'Invalid response from controller.');
            
        $resource->uri = $newUri;
        $resource->save();
        
        $response = $this->call('GET', '/' . $uri);
        $this->assertRedirectedTo('/' . $newUri);
        
        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        $response = $this->call('GET', '/foo/bar');
    }
}
