<?php

namespace MyVendor\Task\Resource\Page;

use BEAR\Resource\ResourceObject;
use Koriym\DbAppPackage\AbstractDatabaseTestCase;

class TaskTest extends AbstractDatabaseTestCase
{
    public function testOnPost()
    {
        $query = ['title' => 'shopping'];
        $page = $this->resource->post->uri('app://self/task')->withQuery($query)->eager->request();
        $this->assertSame(201, $page->code);
        $this->assertArrayHasKey('Location', $page->headers);

        return $page;
    }

    /**
     * @depends testOnPost
     */
    public function testPatch(ResourceObject $page)
    {
        $uri = sprintf('app://self%s', $page->headers['Location']);
        $page = $this->resource->patch->uri($uri)->eager->request();
        $this->assertSame(200, $page->code);

        return $page;
    }

    /**
     * @depends testOnPost
     */
    public function testGet(ResourceObject $page)
    {
        $uri = sprintf('app://self%s', $page->headers['Location']);
        $page = $this->resource->get->uri($uri)->eager->request();
        $this->assertSame('shopping', $page->body['title']);
        $this->assertSame('1', $page->body['completed']);
    }
}
