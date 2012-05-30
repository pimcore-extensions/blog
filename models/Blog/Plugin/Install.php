<?php

/**
 * ModernWeb
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.modernweb.pl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@modernweb.pl so we can send you a copy immediately.
 *
 * @category    Pimcore
 * @package     Plugin_Blog
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * Blog plugin installation class.
 *
 * @category    Pimcore
 * @package     Plugin_Blog
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Blog_Plugin_Install
{
    public function createClass($name)
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . "/Blog/install/class_$name.xml");

        $class = Object_Class::create();
        $class->setName($name);
        $class->setUserOwner(self::_getUser()->getId());
        $class->setLayoutDefinitions(
            Object_Class_Service::generateLayoutTreeFromArray(
                $conf->layoutDefinitions->toArray()
            )
        );
        $class->setIcon($conf->icon);
        $class->setAllowInherit($conf->allowInherit);
        $class->setAllowVariants($conf->allowVariants);
        $class->setParentClass($conf->parentClass);
        $class->setPreviewUrl($conf->previewUrl);
        $class->setPropertyVisibility($conf->propertyVisibility);
        $class->save();

        return $class;
    }

    public function setClassmap()
    {
        try {
            $conf = new Zend_Config_Xml(PIMCORE_CONFIGURATION_DIRECTORY . '/classmap.xml');
            $classmap = $conf->toArray();
        } catch(Exception $e) {
            $classmap = array();
        }

        $classmap['Object_BlogEntry'] = 'Blog_Entry';

        $writer = new Zend_Config_Writer_Xml(array(
            'config' => new Zend_Config($classmap),
            'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/classmap.xml'
        ));
        $writer->write();
    }

    public function createFolders()
    {
        $root = Object_Folder::create(array(
            'o_parentId' => 1,
            'o_creationDate' => time(),
            'o_userOwner' => self::_getUser()->getId(),
            'o_userModification' => self::_getUser()->getId(),
            'o_key' => 'blog',
            'o_published' => true,
        ));
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => self::_getUser()->getId(),
            'o_userModification' => self::_getUser()->getId(),
            'o_key' => 'entries',
            'o_published' => true,
        ));
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => self::_getUser()->getId(),
            'o_userModification' => self::_getUser()->getId(),
            'o_key' => 'categories',
            'o_published' => true,
        ));

        return $root;
    }

    public function createCustomView($rootFolder, array $classIds)
    {
        $customViews = Pimcore_Tool::getCustomViewConfig();
        if (!$customViews) {
            $customViews = array();
            $customViewId = 1;
        } else {
            $last = end($customViews);
            $customViewId = $last['id'] + 1;
        }
        $customViews[] = array(
            'name' => 'Blog',
            'condition' => '',
            'icon' => '/pimcore/static/img/icon/rss.png',
            'id' => $customViewId,
            'rootfolder' => $rootFolder->getFullPath(),
            'showroot' => false,
            'classes' => implode(',', $classIds),
        );
        $writer = new Zend_Config_Writer_Xml(array(
            'config' => new Zend_Config(array('views'=> array('view' => $customViews))),
            'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/customviews.xml'
        ));
        $writer->write();
    }

    public function importStaticRoutes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/Blog/install/staticroutes.xml');

        foreach ($conf->routes->route as $def) {
            $route = Staticroute::create();
            $route->setName($def->name);
            $route->setPattern($def->pattern);
            $route->setReverse($def->reverse);
            $route->setModule($def->module);
            $route->setController($def->controller);
            $route->setAction($def->action);
            $route->setVariables($def->variables);
            $route->setPriority($def->priority);
            $route->save();
        }
    }

    /**
     * @return User
     */
    protected static function _getUser()
    {
        return Zend_Registry::get('pimcore_user');
    }

}
