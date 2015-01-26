<?php

namespace Sabre\ResourceLocator;

/**
 * This interface describes the contract any resource must fulfill.
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH. (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
interface ResourceInterface {

    /**
     * Returns links for this resource.
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
    function getLinks();

}

