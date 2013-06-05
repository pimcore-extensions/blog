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
    /**
     * @var User
     */
    protected $_user;

    public function createClass($name)
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . "/Blog/install/class_$name.xml", null, true);

        if ($name == 'BlogEntry' && !class_exists('Tagfield_Plugin')) {
            unset($conf->layoutDefinitions->childs->childs->{4});
        }

        $class = Object_Class::create();
        $class->setName($name);
        $class->setUserOwner($this->_getUser()->getId());
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

    public function removeClass($name)
    {
        $class = Object_Class::getByName($name);
        if ($class) {
            $class->delete();
        }
    }

    public function setClassmap()
    {
        $classmapXml = PIMCORE_CONFIGURATION_DIRECTORY . '/classmap.xml';

        try {
            $conf = new Zend_Config_Xml($classmapXml);
            $classmap = $conf->toArray();
        } catch(Exception $e) {
            $classmap = array();
        }

        $classmap['Object_BlogEntry'] = 'Blog_Entry';

        $writer = new Zend_Config_Writer_Xml(array(
            'config' => new Zend_Config($classmap),
            'filename' => $classmapXml
        ));
        $writer->write();
    }

    public function unsetClassmap()
    {
        $classmapXml = PIMCORE_CONFIGURATION_DIRECTORY . '/classmap.xml';

        try {
            $conf = new Zend_Config_Xml($classmapXml);
            $classmap = $conf->toArray();
            unset($classmap['Object_BlogEntry']);

            $writer = new Zend_Config_Writer_Xml(array(
                'config' => new Zend_Config($classmap),
                'filename' => $classmapXml
            ));
            $writer->write();
        } catch(Exception $e) {}
    }

    public function createFolders()
    {
        $root = Object_Folder::create(array(
            'o_parentId' => 1,
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'blog',
            'o_published' => true,
        ));
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'entries',
            'o_published' => true,
        ));
        Object_Folder::create(array(
            'o_parentId' => $root->getId(),
            'o_creationDate' => time(),
            'o_userOwner' => $this->_getUser()->getId(),
            'o_userModification' => $this->_getUser()->getId(),
            'o_key' => 'categories',
            'o_published' => true,
        ));

        return $root;
    }

    public function removeFolders()
    {
        $blogFolder = Object_Folder::getByPath('/blog');
        if ($blogFolder) {
            $blogFolder->delete();
        }
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

    public function removeCustomView()
    {
        $customViews = Pimcore_Tool::getCustomViewConfig();
        if ($customViews) {
            foreach ($customViews as $key => $view) {
                if ($view['name'] == 'Blog') {
                    unset($customViews[$key]);
                    break;
                }
            }
            $writer = new Zend_Config_Writer_Xml(array(
                'config' => new Zend_Config(array('views'=> array('view' => $customViews))),
                'filename' => PIMCORE_CONFIGURATION_DIRECTORY . '/customviews.xml'
            ));
            $writer->write();
        }
    }

    public function createStaticRoutes()
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

    public function removeStaticRoutes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/Blog/install/staticroutes.xml');

        foreach ($conf->routes->route as $def) {
            $route = Staticroute::getByName($def->name);
            if ($route) {
                $route->delete();
            }
        }
    }

    public function createDocTypes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/Blog/install/doctypes.xml');

        foreach ($conf->doctypes->doctype as $def) {
            $docType = Document_DocType::create();
            $docType->setName($def->name);
            $docType->setType($def->type);
            $docType->setModule($def->module);
            $docType->setController($def->controller);
            $docType->setAction($def->action);
            $docType->save();
        }
    }

    public function removeDocTypes()
    {
        $conf = new Zend_Config_Xml(PIMCORE_PLUGINS_PATH . '/Blog/install/doctypes.xml');

        $names = array();
        foreach ($conf->doctypes->doctype as $def) {
            $names[] = $def->name;
        }

        $list = new Document_DocType_List();
        $list->load();

        foreach ($list->docTypes as $docType) {
            /* @var $docType Document_DocType */
            if (in_array($docType->name, $names)) {
                $docType->delete();
            }
        }
    }

    /**
     * @return User
     */
    protected function _getUser()
    {
        if (!$this->_user) {
            $this->_user = Zend_Registry::get('pimcore_admin_user');
        }

        return $this->_user;
    }

}
