<?php

namespace Sabre\ResourceLocator;

/**
 * The 'CollectionInterface' is a type of Resource that can contains members.
 * The members can be fetched using the getItem() method. 
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH. (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
interface CollectionInterface extends ResourceInterface {

    /**
     * Returns a collection item based on its name.
     *
     * Return null if the child does not exist.
     *
     * @param string $name
     * @return ResourceInterface|null
     */
    function getItem($name);

}
