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
    function mount($path, ResourceInterface $resource) {

        if (!$resource instanceof ResourceInterface && !is_callable($resource)) {
            throw new InvalidArgumentException('A mounted resource must be either ResourceInterface or a callback');
        }
        $this->mounts[$path] = $resource;

        list($parent, $child) = Uri::split($path);
        $this->addLink($parent, 'child', $child);

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
     * This method will return null if the resource did not exists.
     *
     * @param string $path
     * @return null|ResourceInterface
     */
    function get($path) {

        if (isset($this->mounts[$path])) {
            if ($this->mounts[$path] instanceof ResourceInterface) {
                return $this->mounts[$path];
            } else {
                $this->mounts[$path] = $this->mounts[$path]();
                return $this->mounts[$path];
            }
        }

        // We don't have a mount, we need to reverse-traverse the tree to find
        // the original node.
        list($parentName, $baseName) = Uri::split($path);

        $parent = $this->get($parentName);
        if (is_null($parent)) {
            return null;
        }
        if (!$parent instanceof ParentResourceInterface) {
            return null;
        }
        return $parent->getChild($name);

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
     * If the URI is relative and starts with a slash, the 'root' of the
     * resource locator is used to determine the real path. If the URI is
     * relative, but does not start with a slash, the location of the 'parent
     * node' is taken as the base path.
     *
     * @param mixed $path
     * @return array
     */
    function getLinks($path) {

        return $this->get($path)->getLinks();

    }

}
