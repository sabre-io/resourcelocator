<?php

namespace Sabre\ResourceLocator;

class LocatorTest extends \PHPUnit_Framework_TestCase {

    function testConstruct() {

        $locator = new Locator();
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('')
        );

    }

    /**
     * @depends testConstruct
     * @expectedException \Sabre\ResourceLocator\NotFoundException
     */
    function testGetNotFound() {

        $locator = new Locator();
        $locator->get('foo');

    }

    /**
     * @depends testGetNotFound
     */
    function testMountResource() {

        $locator = new Locator();
        $locator->mount('foo', new NullResource());
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('foo')
        );
        $this->assertEquals(
            [
                new Link('foo','item')
            ],
            $locator->getLinks('')
        );
        $this->assertEquals(
            [
                new Link('','collection')
            ],
            $locator->getLinks('foo')
        );

    }

    /**
     * @depends testGetNotFound
     */
    function testMountCallBack() {

        $locator = new Locator();
        $locator->mount('foo', function() { return new NullResource(); });
        $this->assertInstanceOf(
            NullResource::class,
            $locator->get('foo')
        );
        $this->assertEquals(
            [
                new Link('foo','item')
            ],
            $locator->getLinks('')
        );

    }

    /**
     * @depends testConstruct
     * @expectedException InvalidArgumentException
     */
    function testMountInvalid() {

        $locator = new Locator();
        $locator->mount('foo', 'blabla');

    }

    /**
     * @depends testMountResource
     */
    function testLink() {

        $locator = new Locator();
        $locator->mount('foo', new NullResource());
        $locator->link('foo', new Link('http://evertpot.com/', 'homepage'));
        $locator->link('foo', new Link('http://evertpot.com/', 'homepage'));
        $this->assertEquals(
            [
                new Link('','collection'),
                new Link('http://evertpot.com/','homepage'),
                new Link('http://evertpot.com/','homepage')
            ],
            $locator->getLinks('foo')
        );

    }

    /**
     * @depends testMountResource
     */
    function testGetFromParentResource() {

        $parent = $this->getMock('Sabre\ResourceLocator\CollectionInterface');
        $parent->expects($this->once())->method('getItem')->willReturn(function() { return new NullResource(); });

        $locator = new Locator();
        $locator->mount('parent', $parent);

        $locator->get('parent/child');

    }

}
