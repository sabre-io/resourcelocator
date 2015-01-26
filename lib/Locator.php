<?php

namespace Sabre\ResourceLocator;

use InvalidArgumentException;
use Sabre\Uri;

/**
 * The Locator is the standard implementation of the resource-locator.
 *
 * @copyright Copyright (C) 2015 fruux GmbH. (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Locator implements LocatorInterface {

    /**
     * Mounted paths.
     *
     * @var array
     */
    protected $mounts = [];

    /**
     * Contains a list of links.
     *
     * @var string
     */
    protected $links = [];

    /**
     * Creates and initializes the Locator
     */
    function __construct() {

        $this->mounts[''] = new NullResource();

    }

    /**
     * Mounts a resource on a specific path.
     *
     * The resource will be returned when requesting the specific path, or when
     * fetching links for its parent.
     *
     * The resource may either be immediately specfied or a callback may be
     * used to allow for lazy-loading.
     *
     * @param string $path
     * @param ResourceInterface $resource
     * @return void
     */
    function mount($path, $resource) {

        if (!$resource instanceof ResourceInterface && !is_callable($resource)) {
            throw new InvalidArgumentException('A mounted resource must be either ResourceInterface or a callback');
        }
        $this->mounts[$path] = $resource;

        list($parent, $child) = Uri\split($path);
        $this->link($parent, 'child', $path);

    }

    /**
     * Adds a link into the tree.
     *
     * @param string $origin Where the link comes from.
     * @param string $relation The type of relationship.
     * @param string $destination Where we're linking to.
     * @return void
     */
    function link($origin, $relation, $destination) {

        if (!isset($this->links[$origin])) {
            $this->links[$origin] = [$relation => [$destination]];
        } elseif (!isset($this->links[$origin][$relation])) {
            $this->links[$origin][$relation] = [$destination];
        } else {
            $this->links[$origin][$relation][] = $destination;
        }

    }

    /**
     * Returns a resource for a specific path.
     *
     * This method will throw a NotFoundException if the resource could not be
     * found.
     *
     * @param string $path
     * @throws NotFoundException
     * @return ResourceInterface
     */
    function get($path) {

        if (isset($this->mounts[$path])) {
            // We have a mount on that path.
            if ($this->mounts[$path] instanceof ResourceInterface) {
                return $this->mounts[$path];
            } else {
                // The mount was specified as a callback, so we're running it
                // now, and saving the result.
                $this->mounts[$path] = $this->mounts[$path]();
                return $this->mounts[$path];
            }
        }

        // We don't have a mount, we need to reverse-traverse the tree to find
        // the original node.
        list($parentName, $baseName) = Uri\split($path);

        if (!$parentName) {
            // We're at the root and can't go up further in the tree.
            throw new NotFoundException('Resource not found');
        }

        $parent = $this->get($parentName);
        if (is_null($parent)) {
            // The parent did not exist.
            throw new NotFoundException('Resource not found');
        }
        if (!$parent instanceof ParentResourceInterface) {
            // The parent was not a 'ParentResource'.
            throw new NotFoundException('Resource not found');
        }
        $result = $parent->getChild($baseName);
        if (is_null($result)) {
            throw new NotFoundException('Resource not found');
        } else {
            return $result;
        }

    }

    /**
     * Returns whether a resource on a specific path exists.
     *
     * @param string $path
     * @return void
     */
    function exists($path) {

        return !is_null($this->get($path));

    }

    /**
     * Returns links for a particular resource.
     *
     * While this is optional, adding this feature allows for automated
     * discovery of resources associated with this particular resource.
     *
     * The returned data-structure must be an array, whose keys are the 'type'
     * of relation, and whose values is another array with URIs.
     *
     * URIs may be absolute or relative.
     *
     * @param mixed $path
     * @return array
     */
    function getLinks($path) {

        $rLinks = [];

        if ($path) {
            // A non-empty path means that a parent exists
            $rLinks['parent'] = [Uri\split($path)[0]];
        }

        // Links coming from the node might be relative to the node, this
        // function makes them relative to the root of the locator.
        foreach($this->get($path)->getLinks() as $rel=>$links) {

            if (!isset($rLinks[$rel])) {
                $rLinks[$rel] = [];
            }
            foreach($links as $link) {
                if(parse_url($link, PHP_URL_SCHEME)) {
                    // Absolute
                    $rLinks[$rel][] = $link;
                } elseif ($link[0] === '/') {
                    // Relative to the root of the locator.
                    $rLinks[$rel][] = substr($link,1);
                } else {
                    // Relative to the current node.
                    $rLinks[$rel][] = $path . '/' . $link;
                }
            }

        }

        return array_merge_recursive(
            $rLinks,
            isset($this->links[$path])?$this->links[$path]:[]
        );

    }

}
