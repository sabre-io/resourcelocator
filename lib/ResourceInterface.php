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

     * @return LinkInterface[]
     */
    function getLinks();

}

