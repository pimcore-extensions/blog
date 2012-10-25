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
 * @subpackage  Controller
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 * @license     http://www.modernweb.pl/license/new-bsd     New BSD License
 */

/**
 * @category    Pimcore
 * @package     Plugin_Blog
 * @subpackage  Controller
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Blog_SnippetController extends Blog_Controller_Action
{
    /**
     * @var Blog
     */
    protected $_blog;

    public function init()
    {
        parent::init();

//        $options = Pimcore_Config::getWebsiteConfig(false)->blog;
        $options = array();
        $this->_blog = new Blog($options);
    }

    public function latestAction()
    {
        $limit = (int) $this->document->getProperty('limit');
        if (!$limit) {
            $limit = 3;
        }

        $this->view->entries = $this->_blog->getList(1, $limit)->getCurrentItems();
    }

    public function calendarAction()
    {
        // $this->_request doesn't have params from staticroute
        $request = $this->getFrontController()->getRequest();

        $this->view->calendar = $this->_blog->getCalendar();
        $this->view->year = $request->getParam('year');
        $this->view->month = $request->getParam('month');
    }

    public function categoriesAction()
    {
        // $this->_request doesn't have params from staticroute
        $request = $this->getFrontController()->getRequest();

        $this->view->list = $this->_blog->getCategories();
        $this->view->category = $request->getParam('cat');
    }

    public function feedAction()
    {
    }

}
