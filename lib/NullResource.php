<?php

namespace Sabre\ResourceLocator;

/**
 * A very simple implementation of a resource.
 *
 * All this resource does, is exists.
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH. (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class NullResource implements ResourceInterface {

    function getLinks() {

        return [];

    }

}
