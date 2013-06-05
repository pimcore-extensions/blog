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
 * Core plugin class.
 *
 * @category    Pimcore
 * @package     Plugin_Blog
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Blog_Plugin extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface
{
    /**
     * @var Zend_Translate
     */
    protected static $_translate;

    /**
     * @return string $statusMessage
     */
    public static function install()
    {
        try {
            $install = new Blog_Plugin_Install();

            // create object classes
            $blogCategory = $install->createClass('BlogCategory');
            $blogEntry = $install->createClass('BlogEntry');

            // classmap
            $install->setClassmap();

            // create root object folder with subfolders
            $blogFolder = $install->createFolders();

            // create custom view for blog objects
            $install->createCustomView($blogFolder, array(
                $blogEntry->getId(),
                $blogCategory->getId(),
            ));

            // create static routes
            $install->createStaticRoutes();

            // create predefined document types
            $install->createDocTypes();

        } catch(Exception $e) {
            logger::crit($e);
            return self::getTranslate()->_('blog_install_failed');
        }

        return self::getTranslate()->_('blog_installed_successfully');
    }

    /**
     * @return string $statusMessage
     */
    public static function uninstall()
    {
        try {
            $install = new Blog_Plugin_Install();

            // remove predefined document types
            $install->removeDocTypes();

            // remove static routes
            $install->removeStaticRoutes();

            // remove custom view
            $install->removeCustomView();

            // remove object folder with all childs
            $install->removeFolders();

            // classmap
            $install->unsetClassmap();

            // remove classes
            $install->removeClass('BlogEntry');
            $install->removeClass('BlogCategory');

            return self::getTranslate()->_('blog_uninstalled_successfully');
        } catch (Exception $e) {
            Logger::crit($e);
            return self::getTranslate()->_('blog_uninstall_failed');
        }
    }

    /**
     * @return boolean $isInstalled
     */
    public static function isInstalled()
    {
        $entry = Object_Class::getByName('BlogEntry');
        $category = Object_Class::getByName('BlogCategory');

        if ($entry && $category) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public static function getTranslationFileDirectory()
    {
        return PIMCORE_PLUGINS_PATH . '/Blog/static/texts';
    }

    /**
     * @param string $language
     * @return string path to the translation file relative to plugin direcory
     */
    public static function getTranslationFile($language)
    {
        if (is_file(self::getTranslationFileDirectory() . "/$language.csv")) {
            return "/Blog/static/texts/$language.csv";
        } else {
            return '/Blog/static/texts/en.csv';
        }
    }

    /**
     * @return Zend_Translate
     */
    public static function getTranslate()
    {
        if(self::$_translate instanceof Zend_Translate) {
            return self::$_translate;
        }

        try {
            $lang = Zend_Registry::get('Zend_Locale')->getLanguage();
        } catch (Exception $e) {
            $lang = 'en';
        }

        self::$_translate = new Zend_Translate(
            'csv',
            PIMCORE_PLUGINS_PATH . self::getTranslationFile($lang),
            $lang,
            array('delimiter' => ',')
        );
        return self::$_translate;
    }

}
