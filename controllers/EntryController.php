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
class Blog_EntryController extends Blog_Controller_Action
{
    /**
     * @var Blog
     */
    protected $_blog;

    /**
     * @var Commenting
     */
    protected $_commenting;

    public function init()
    {
        parent::init();

        $this->_blog = new Blog();

        if (class_exists('Commenting')) {
            $this->_commenting = new Commenting();
        }
    }

    /**
     * Preview entry in pimcore admin.
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function previewAction()
    {
        $id = (int) $this->_getParam('o_id');
        $entry = Object_Abstract::getById($id);
        /* @var $entry Blog_Entry */

        if (null == $entry) {
            throw new Zend_Controller_Action_Exception("No entry with ID '$id'", 404);
        }

        return $this->_forward('show', null, null, array(
            'key' => $entry->getUrlPath()
        ));
    }

    /**
     * Show entry.
     *
     */
    public function showAction()
    {
        $this->enableLayout();

        $key = $this->_getParam('key');
        $entry = $this->_blog->getEntry($key);
        // @todo pimcore Staticroute::assemble() generates double slash on end
        $url = rtrim($this->view->url(), '/') . '/';

        if (!$entry) {
            throw new Zend_Controller_Action_Exception("Blog entry for key '$key' not found", 404);
        }

        if ($this->_commenting && $this->_request->isPost()) {
            $result = $this->_commenting->saveComment($this->_request->getPost(), $entry);
            if ($result) {
                $this->_messenger->addMessage(
                    $this->_translate->_('blog_comment_added')
                );
                return $this->_redirect($url);
            }
        }
        $this->view->entry = $entry;

        if ($this->_commenting) {
            $this->view->comments = $this->_commenting->getComments(
                $entry, $this->_getParam('page', 1), $this->_getParam('perpage', 10)
            );
            $form = $this->_commenting->getForm();
            $form->setAction($url);
            $this->view->commentForm = $form;
        }

        $this->view->headTitle($entry->getTitle());
    }

    /**
     * Entry list.
     *
     */
    public function defaultAction()
    {
        $this->enableLayout();

        $this->view->paginator = $this->_blog->getList(
            $this->_getParam('page', 1),
            $this->_getParam('perpage', 10)
        );
    }

    /**
     * Entry list by category.
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function categoryAction()
    {
        $this->enableLayout();

        $cat = $this->_getParam('cat');
        $category = Object_BlogCategory::getByPath('/blog/categories/' . $cat);
        if (!$category) {
            throw new Blog_Exception("Category $cat doesn't exist");
        }

        try {
            $this->view->paginator = $this->_blog->getListByCategory(
                $category,
                $this->_getParam('page', 1),
                $this->_getParam('perpage', 10)
            );
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $this->view->category = $category;

        $this->render('default');
    }

    /**
     * Entry list by tag.
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function tagAction()
    {
        $this->enableLayout();

        try {
            $this->view->paginator = $this->_blog->getListByTag(
                $this->_getParam('tag'),
                $this->_getParam('page', 1),
                $this->_getParam('perpage', 10)
            );
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $this->render('default');
    }

    /**
     * Entry list by year/month.
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function calendarAction()
    {
        $this->enableLayout();

        try {
            $this->view->paginator = $this->_blog->getListByDate(
                $this->_getParam('year'),
                $this->_getParam('month'),
                $this->_getParam('page', 1),
                $this->_getParam('perpage', 10)
            );
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $this->render('default');
    }

    /**
     * Entry feed (RSS|ATOM).
     *
     * @throws Zend_Controller_Action_Exception
     */
    public function feedAction()
    {
        try {
            $feed = $this->_blog->getFeed($this->_getParam('type', 'rss'));
        } catch (Exception $e) {
            throw new Zend_Controller_Action_Exception($e->getMessage(), 404);
        }

        $feed->send();
        exit;
    }

}
