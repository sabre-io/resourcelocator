<?php

namespace Sabre\ResourceLocator;

use PHPUnit\Framework\TestCase;

class LocatorTest extends TestCase
{
    public function testConstruct()
    {
        $locator = new Locator();
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('')
        );
    }

    /**
     * @depends testConstruct
     */
    public function testGetNotFound()
    {
        $this->expectException(NotFoundException::class);
        $locator = new Locator();
        $locator->get('foo');
    }

    /**
     * @depends testGetNotFound
     */
    public function testMountResource()
    {
        $locator = new Locator();
        $locator->mount('foo', new NullResource());
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('foo')
        );
        $this->assertEquals(
            [
                new Link('foo', 'item'),
            ],
            $locator->getLinks('')
        );
        $this->assertEquals(
            [
                new Link('', 'collection'),
            ],
            $locator->getLinks('foo')
        );
    }

    /**
     * @depends testGetNotFound
     */
    public function testMountCallBack()
    {
        $locator = new Locator();
        $locator->mount('foo', function () { return new NullResource(); });
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('foo')
        );
        $this->assertEquals(
            [
                new Link('foo', 'item'),
            ],
            $locator->getLinks('')
        );
    }

    /**
     * @depends testConstruct
     */
    public function testMountInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $locator = new Locator();
        $locator->mount('foo', 'blabla');
    }

    /**
     * @depends testMountResource
     */
    public function testLink()
    {
        $locator = new Locator();
        $locator->mount('foo', new NullResource());
        $locator->link('foo', new Link('http://evertpot.com/', 'homepage'));
        $locator->link('foo', new Link('http://evertpot.com/', 'homepage'));
        $this->assertEquals(
            [
                new Link('', 'collection'),
                new Link('http://evertpot.com/', 'homepage'),
                new Link('http://evertpot.com/', 'homepage'),
            ],
            $locator->getLinks('foo')
        );
    }

    /**
     * @depends testMountResource
     */
    public function testGetFromParentResource()
    {
        $parent = $this->getMockBuilder('Sabre\ResourceLocator\CollectionInterface')
            ->getMock();
        $parent->expects($this->once())
            ->method('getItem')
            ->willReturn(new NullResource());

        $locator = new Locator();
        $locator->mount('parent', $parent);

        $result = $locator->get('parent/child');
        $this->assertInstanceOf(NullResource::class, $result);
    }

    /**
     * @depends testMountResource
     */
    public function testGetLinksViaResource()
    {
        $resource = $this->getMockBuilder('Sabre\ResourceLocator\NullResource')
            ->getMock();
        $resource
            ->expects($this->once())
            ->method('getLinks')
            ->willReturn([
                new Link('http://example.org', 'foo-bar'),
                new Link('/', 'root'),
                new Link('subnode', 'child'),
            ]);

        $locator = new Locator();
        $locator->mount('node', $resource);

        $this->assertEquals(
            [
                new Link('', 'collection'),
                new Link('http://example.org', 'foo-bar'),
                new Link('', 'root'),
                new Link('node/subnode', 'child'),
            ],
            $locator->getLinks('node')
        );
    }
}
