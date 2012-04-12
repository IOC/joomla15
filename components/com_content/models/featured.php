<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/articles.php';

/**
 * Frontpage Component Model
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class ContentModelFeatured extends ContentModelArticles
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	public $_context = 'com_content.frontpage';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);

		// List state information
		$limitstart = JRequest::getUInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		$params = $this->state->params;
		$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
		$this->setState('list.limit', $limit);
		$this->setState('list.links', $params->get('num_links'));

		$this->setState('filter.frontpage', true);

		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_content')) &&  (!$user->authorise('core.edit', 'com_content'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}
		else {
			$this->setState('filter.published', array(0, 1, 2));
		}

		// check for category selection
		if ($params->get('featured_categories') && implode(',', $params->get('featured_categories'))  == true) {
			$featuredCategories = $params->get('featured_categories');
 			$this->setState('filter.frontpage.categories', $featuredCategories);
 		}
	}

	/**
	 * Method to get a list of articles.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function getItems()
	{
		$params = clone $this->getState('params');
		$limit = $params->get('num_leading_articles') + $params->get('num_intro_articles') + $params->get('num_links');
		if ($limit > 0)
		{
			$this->setState('list.limit', $limit);
			return parent::getItems();
		}
		return array();

	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= $this->getState('filter.frontpage');

		return parent::getStoreId($id);
	}

	/**
	 * @return	JDatabaseQuery
	 */
	function getListQuery()
	{
		// Set the blog ordering
		$params = $this->state->params;
		$articleOrderby = $params->get('orderby_sec', 'rdate');
		$articleOrderDate = $params->get('order_date');
		$categoryOrderby = $params->def('orderby_pri', '');
		$secondary = ContentHelperQuery::orderbySecondary($articleOrderby, $articleOrderDate) . ', ';
		$primary = ContentHelperQuery::orderbyPrimary($categoryOrderby);

		$orderby = $primary . ' ' . $secondary . ' a.created DESC ';
		$this->setState('list.ordering', $orderby);
		$this->setState('list.direction', '');
		// Create a new query object.
		$query = parent::getListQuery();

		// Filter by frontpage.
		if ($this->getState('filter.frontpage'))
		{
			$query->join('INNER', '#__content_frontpage AS fp ON fp.content_id = a.id');
		}

		// Filter by categories
		if (is_array($featuredCategories = $this->getState('filter.frontpage.categories'))) {
			$query->where('a.catid IN (' . implode(',', $featuredCategories) . ')');
		}


		return $query;
	}

	//Funcio que ens retorna els articles amb les noticies de matricula
	function getMatricula()
	{
            $params = clone $this->getState('params');
		    $id_category = $params->get('id_category');
		    $id_calendar = $params->get('id');

            $query = 'SELECT title'.
                        ' FROM #__categories'.
                        ' WHERE id ='.(int) $id_category;
            $Arows = $this->_getList($query);

            //Agafem l'unic element
            $categoria = array_pop($Arows);

            $query = 'SELECT id, title, created, modified'.
                        ' FROM #__content'.
                        ' WHERE state = 1'.
                        ' AND catid ='. (int) $id_category .
                        ' ORDER BY ordering ASC';
            $articles = $this->_getList($query);

            $cat = new stdClass();
            $cat->calendar = $id_calendar;
            $cat->title = $categoria->title;
            $cat->data = array();
            foreach ($articles as $art){
                $cat->data[$art->id] = new stdClass();
                $cat->data[$art->id]->title = $art->title;
                if (intval($art->modified) != 0){
                    $modified = $art->modified;
                }else{
                    $modified = $art->created;
                }
                $cat->data[$art->id]->modified = $modified;
            }
            return $cat;
	}
}
