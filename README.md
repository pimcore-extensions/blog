# Blog plugin for [pimcore](http://www.pimcore.org/) #

It's functional but still under development. Try, comment, fork, improve or stay tuned! :)

## Features (status) ##

*   Entries and categories build on pimcore [data objects](http://www.pimcore.org/wiki/display/PIMCORE/Data+Objects) **(ready)**
*   RSS feed **(ready)**
*   Snippets with categories / calendar / feed links / latest entries **(in progress)**
*   Tags and tag cloud provided by [Tags Field V2](http://www.pimcore.org/resources/extensions/detail/Tagfield) plugin integration **(soon)**
*   Comments provided by [Commenting](http://www.pimcore.org/resources/extensions/detail/Commenting) plugin integration **(soon)**

## Installation ##

Just like other Pimcore plugins:

*   navigate to `Extras -> Extensions -> Download Extensions`, find Blog plugin on list and choose "Download"
    **(plugin will be available in Extension Hub after first stable release)**
*   [OR] clone git repository into `/plugin/Blog` directory
*   navigate to `Extras -> Extensions -> Manage Extensions` in admin panel
*   enable & install plugin
*   reload admin interface

If everything went smoothly you will find `Blog` custom view just below `Objects` in right panel.
Now you can add some entries/categories.

Create document named eg. `Blog` and set in `Settings` -> `Controller and View Settings`:
```
Module: Blog
Controller: entry
Action: default
```

## Customization ##

You can add/modify/remove entry/category data fields using default pimcore Object Classes interface.

You can overwrite any of default view scripts by copying `Blog/views/scripts/:controller/:action.php` into `website/views/scripts/blog/:controller/:action.php`.
Your custom view script will be used automatically instead of default one. This will allow you to update plugin without loosing your project markup.
