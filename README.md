sabre/resource-locator
======================

This repository provides a 'resource locator'. The function of the
resource-locator is not unlike a 'router' in web framework terms.

This resource locator forces a strict tree-based hierarchy.

The core functionality of this package is to 'take' some kind of path,
such as '/article/12' and map this to a class representing this resource.

Unlike typical routers, this resource locator only facilitates fetching a
class living at a particular location, but does not make any assumption about
what processes a particular request, and send a response.


Installation
------------

Make sure you have [composer][1] installed, and then run:

    composer require sabre/resource-locator


Build status
------------

| branch | status |
| ------ | ------ |
| master | [![Build Status](https://travis-ci.org/fruux/sabre-resource-locator.png?branch=master)](https://travis-ci.org/fruux/sabre-resource-locator) |


Questions?
----------

Head over to the [sabre/dav mailinglist][2], or you can also just open a ticket
on [GitHub][3].


Made at fruux
-------------

This library is being developed by [fruux][4]. Drop us a line for commercial
services or enterprise support.

[1]: http://getcomposer.org/
[2]: http://groups.google.com/group/sabredav-discuss
[3]: https://github.com/fruux/sabre-resource-locator/issues/
[4]: https://fruux.com/
