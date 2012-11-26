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
 * @category    Pimcore
 * @package     Plugin_Blog
 * @author      Rafał Gałka <rafal@modernweb.pl>
 * @copyright   Copyright (c) 2007-2012 ModernWeb (http://www.modernweb.pl)
 */
class Blog
{
    /**
     * @var Zend_Config
     */
    protected $_options;

    /**
     * @param array|Zend_Config $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * @param array|Zend_Config $options
     * @return \Blog
     * @throws Blog_Exception
     */
    public function setOptions($options)
    {
        if (is_array($options)) {
            $options = new Zend_Config($options);
        }

        if (!$options instanceof Zend_Config) {
            throw new Blog_Exception('Options must be array or Zend_Config instance');
        }

        $this->_options = $options;

        return $this;
    }

    /**
     * @return Zend_Config
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @param string $key
     * @return Blog_Entry
     */
    public function getEntry($key)
    {
        return Object_Abstract::getByPath('/blog/entries/' . $key);
    }

    /**
     * @param integer $page
     * @param integer $perPage
     * @return Zend_Paginator
     */
    public function getList($page = 1, $perPage = 10)
    {
        return $this->_paginate($this->_getList(), $page, $perPage);
    }

    /**
     * @param string $tag
     * @param integer $page
     * @param integer $perPage
     * @return Zend_Paginator
     */
    public function getListByTag($tag, $page = 1, $perPage = 10)
    {
        $list = $this->_getList();
        $list->setCondition("tags LIKE ?", array("%,{$tag},%"));

        return $this->_paginate($list, $page, $perPage);
    }

    /**
     * @param Object_BlogCategory $category
     * @param integer $page
     * @param integer $perpage
     * @return Zend_Paginator
     */
    public function getListByCategory(Object_BlogCategory $category, $page = 1, $perpage = 10)
    {
        $list = $this->_getList();
        $list->setCondition("categories LIKE ?", array("%,{$category->getId()},%"));

        return $this->_paginate($list, $page, $perpage);
    }

    /**
     * @param string $year
     * @param string $month
     * @param integer $page
     * @param integer $perPage
     * @return Zend_Paginator
     */
    public function getListByDate($year, $month = null, $page = 1, $perPage = 10)
    {
        $month = (int) $month;

        if ($month < 1 || $month > 12) {
            $month = 0;
            $date = new Zend_Date("$year-01-01", Zend_Date::ISO_8601);
        } else {
            if (1 == strlen($month)) {
                $month = "0$month";
            }
            $date = new Zend_Date("$year-$month-01", Zend_Date::ISO_8601);
        }

        $from = (int) $date->getTimestamp();
        if ($month) {
            $to = (int) $date->setDay($date->get(Zend_Date::MONTH_DAYS))->getTimestamp();
        } else {
            $to = (int) $date->setMonth(12)->setDay(31)->getTimestamp();
        }

        $list = $this->_getList();
        $list->setCondition('date BETWEEN ? AND ?', array($from, $to));

        return $this->_paginate($list, $page, $perPage);
    }

    /**
     * @return Object_BlogCategory_List
     */
    public function getCategories()
    {
        $list = new Object_BlogCategory_List();
        $limit = (int) @$this->_options->snippet->category->limit;
        $list->setLimit($limit ? $limit : 10);

        $ids = $return = array();
        foreach ($list as $cat) {
            $ids[] = $cat->getId();
        }

        if (empty($ids)) {
            return $list;
        }

        // count entries in categories
        $entry = new Object_BlogEntry();
        $select = new Zend_Db_Select(Pimcore_Resource_Mysql::get()->getResource());
        $select
            ->from('object_relations_' . $entry->getClassId(), array(
                'id' => 'dest_id', 'count' => 'count(*)'
            ))
            ->where('dest_id IN (?)', $ids)
            ->group('dest_id');
        $counts = array();
        foreach ($select->query()->fetchAll() as $row) {
            $counts[$row['id']] = (int) $row['count'];
        }

        foreach ($list as $cat) {
            $count = (isset($counts[$cat->getId()])) ? $counts[$cat->getId()] : 0;
            $cat->setEntryCount($count);
        }

        return $list;
    }

    /**
     * @return array
     * @todo cache management (low priority: time for 6 year calendar
     * on development server with Core 2 Duo 3GHz processor = ~0.4s)
     */
    public function getCalendar()
    {
        $calendar = array();

        $count = $this->_getList()->count();

        if (!$count) {
            return $calendar;
        }

        // date of first entry (decremented by months to $endDate)
        $date = clone $this->getList(1, 1)->getIterator()->current()->getDate();
        $endDate = $this->getList($count, 1)->getIterator()->current()->getDate();

        while (1) {
            $year = $date->get(Zend_Date::YEAR);

            $list = $this->_getList();
            $list->setCondition('date BETWEEN ? AND ?', array(
                (int) $date->setDay(1)->getTimestamp(),
                (int) $date->setDay($date->get(Zend_Date::MONTH_DAYS))->getTimestamp(),
            ));
            $count = $list->count();

            if ($count) {
                if (!isset($calendar[$year])) {
                    $calendar[$year] = array();
                }
                $month = $date->get(Zend_Date::MONTH_SHORT);
                $calendar[$year][$month] = array(
                    'month' => Zend_Locale_Data::getContent(
                        Zend_Registry::get('Zend_Locale'),
                        'month', array('gregorian', 'format', 'wide', $month)
                    ),
                    'count' => $count,
                );
            }

            if ($date->isEarlier($endDate)) {
                break;
            }
            $date->subMonth(1);
        }

        return $calendar;
    }

    /**
     * @param string $format
     * @return Zend_Feed_Abstract
     */
    public function getFeed($format = 'rss')
    {
        if ($format != 'rss' && $format != 'atom') {
            throw new Zend_Controller_Action_Exception("Feed type $format is not supported");
        }

        $url = new Pimcore_View_Helper_Url();

        $host = 'http://' . $_SERVER['HTTP_HOST'];
        $feed = array(
            'title' => '',
            'copyright' => '',
            'link' => $host . 'plugin/Blog/entry/feed/format/' . $format,
            'charset' => 'utf-8',
            'language' => 'pl-pl',
            'lastUpdate' => time(),
            'published' => time(),
            'entries' => array(),
        );
        if ($this->_options->feed) {
            $feed = array_merge($feed, $this->_options->feed->toArray());
        }

        foreach ($this->getList() as $entry) {
            $entry instanceof Object_BlogEntry;
            $feed['entries'][] = array(
                'title' => $entry->getTitle(),
                'link' => $host . $url->url(array('key' => $entry->getKey()), 'blog-show'),
                'description' => (trim($entry->getSummary()))
                    ? $entry->getSummary()
                    : Website_Tool_Text::cutStringRespectingWhitespace(trim(strip_tags($entry->getContent())), 200),
                'lastUpdate' => $entry->getDate()->getTimestamp(),
            );

            if ($entry->getModificationDate() < $feed['lastUpdate']) {
                $feed['lastUpdate'] = $feed['published'] = $entry->getModificationDate();
            }
        }

        return Zend_Feed::importArray($feed, $format);
    }

    /**
     * @return Object_BlogEntry_List
     */
    protected function _getList()
    {
        $list = new Object_BlogEntry_List();
        $list->setOrderKey(array('date', 'o_id'));
        $list->setOrder(array('DESC', 'ASC'));
        return $list;
    }

    /**
     * @param Object_BlogEntry_List $list
     * @param integer $page
     * @param integer $perPage
     * @return Zend_Paginator
     */
    protected function _paginate(Object_BlogEntry_List $list, $page, $perPage)
    {
        $paginator = new Zend_Paginator($list);
        $paginator->setItemCountPerPage((int) $perPage);
        $paginator->setCurrentPageNumber((int) $page);
        $paginator->setPageRange(5);
        return $paginator;
    }

}
