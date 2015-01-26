<?php

/**
 * The 'ParentResource' is a Resource that can return a child-node.
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH. (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
interface ParentResourceInterface extends ResourceInterface {

    /**
     * Returns a child resource based on its name.
     *
     * Return null if the child does not exist.
     *
     * @param string $name
     * @return ResourceInterface|null
     */
    function getChild($name);

}
