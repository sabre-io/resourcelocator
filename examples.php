<?php

/**
 * This is a very basic example that shows how the locator on a high level
 * works.
 *
 * We set up a simply hierarchy, and then with a few crappy templates, you're
 * able to traverse this tree.
 */
namespace Sabre\ResourceLocator;

include 'vendor/autoload.php';

$path = isset($_GET['path'])?$_GET['path']:'';

/**
 * This is the main locator. You could also call this a 'router' or 'tree'.
 */
$locator = new Locator();

/**
 * This is a very simply resource in the tree (or 'Node' in the tree). This
 * node dynamically generates a list of sub-nodes.
 *
 * In this case we're representing users on a system.
 */
class Principals implements ParentResourceInterface {

    /**
     * A list of users... Normally this would come from a DB.
     */
    public $children = ['admin','user1','user2','user3','user4','user5','user6','user7','user8','evert'];

    /**
     * The getChild method is responsible for returning a Resource class for a
     * child-node.
     */
    function getChild($name) {

        if (in_array($name, $this->children)) {
            // A NullResource is kind of like a dummy resource. It does
            // nothing.
            return new NullResource();
        }

    }

    /**
     * getLinks returns a list of links that *originate* at the current node.
     *
     * A standard type of link is a 'child' link, or a 'parent' link.
     *
     * This 'link' is the same think as a '<link>' element in a HTML document,
     * or a Link: header in a HTTP response, or a <href> element in an Atom
     * response, or a <href> in a WebDAV Multistatus, or a link in a HAL json
     * document.
     */
    function getLinks() {

        return ['child' => $this->children];

    }

}

// You can mount a new node *anywhere* in the tree, so not just on the root,
// just like the Unix filesystem.
$locator->mount('principals', new Principals());

// We're re-using the principals resource to also show up on 'calendars'.
$locator->mount('calendars', new Principals());

// This demonstrates that we can mount anywhere we want. It will show up in the
// tree.
$locator->mount('principals/admin/yourock', new NullResource());

// This demonstrates that we can wrap the resource in a callback, so that the
// resource object is only created when absolutely needed, saving time and
// memory.
$locator->mount('principals/admin/isthisturnedon?', function() { return new NullResource(); });

// This demonstrates that we can also add arbitrary links anywhere in tree.
$locator->link('principals/evert', 'homepage', 'http://evertpot.com/');
$locator->link('principals/evert', 'email', 'mailto:me@evertpot.com');


$resource = $locator->get($path);

if(!$resource) {
    die('Resource not found!');
}

$links = $locator->getLinks($path);

function absolute($link) {

    if (!parse_url($link, PHP_URL_SCHEME)) {
        // Relative url.
        return '?path=' . urlencode($link);
    } else {
        return $link;
    }

}

?>
<!DOCTYPE html!>
<head>
  <title>Resource Locator Tester</title>
</head>

<body>

<em>A very poor system to inspect your resource hierarchy</em>

<h1><?= $path?:'(root)' ?></h1>

<h2>Links</h2>
<?php

foreach($links as $rel=>$links) {

    echo '<h3>', $rel, '</h3>';

    echo '<ul>';

    foreach($links as $link) {

        echo '<li><a href="', absolute($link), '">', ($link?:'(root)'), '</a></li>';

    }

    echo '</ul>';

} ?>

</body>
