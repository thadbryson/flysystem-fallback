Flysystem Fallback Plugin
=========================

Installation
------------

Use Composer:
```
"thadbryson/flysystem-fallback": "@stable"
```

This is a plugin for the Flysystem project. https://github.com/thephpleague/flysystem

It allows you to create a Filesystem hierarchy. If a **path** isn't in one Filesystem it will go to the next one until it finds it. Or runs out of Filesystems.

Here is some example code to set everything up.

### Primary

The ```$primary``` Filesystem is the main one. It gets checked **1st**.

### Fallbacks

The ```$fallback``` Filesystems are ones checked after $primary. They go in order in their array.

### Example

```php
use TCB\Flysystem\Fallback;
use TCB\Flysystem\FallbackPlugin;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

// So client #1 has their own theme.
$primary = new Filesystem(new Adapter('~/themes/client-1'));

// Let's set the fallbacks in order.
$fallbacks = [
    '1'       => new Filesystem(new Adapter('~/themes/red')),
    'default' => new Filesystem(new Adapter('~/themes/default')),
];

// Add the Plugin.
$primary->addPlugin(new FallbackPlugin());

// You can get the Fallback object from the 'getFallback()' plugin method.
$fallback = $primary->getFallback($fallbacks);

// OR - you can just create the Fallback object directly.
$fallback = new Fallback($primary, $fallbacks);
```

### Methods you can call

These ```Filesystem``` methods are available on Fallback. The first Filesystem with the
path found will return the result from that Filesystem or "hit".


NOTE: all these methods return ```FALSE``` if $path is not found.

- has(string $path): Do any of these Filesystems have this path?
- read(string $path): Read file contents from first "hit".
- readStream(string $path): Read a stream of the file contents from first "hit".

Other methods available:

- find(string $path): Returns the Filesystem object of the first hit.
- findIndex(string $path): Returns index string/int of Filesystem with $path. Will return FALSE if not found.
