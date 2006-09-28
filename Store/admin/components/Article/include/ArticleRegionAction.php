<?php

require_once 'Swat/SwatRadioList.php';
require_once 'Swat/SwatControl.php';

/**
 * Actions flydown to control the accessibility of articles by region
 *
 * @package   Store
 * @copyright 2005-2006 silverorange
 */
class ArticleRegionAction extends SwatControl
{
	public $db;

	private $region_flydown;
	private $accessibility_selector;
	private $regions;
	private $items;

	public function init()
	{
		$this->region_flydown = new SwatFlydown($this->id.'_region');
		$this->region_flydown->parent = $this;
		$this->accessibility_selector =
			new SwatRadioList($this->id.'_accessible');

		$this->accessibility_selector->parent = $this;

		$this->regions = SwatDB::getOptionArray(
			$this->db, 'Region', 'title', 'id', 'title');
	}

	public function display()
	{
		$this->init();
		$this->region_flydown->show_blank = false;
		$options = array(0 => 'All');
		$options = $options + $this->regions;
		$this->region_flydown->addOptionsByArray($options);

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->id.'_region';
		$label_tag->setContent('For Region: ');
		$label_tag->display();
		$this->region_flydown->display();

		$this->accessibility_selector->addOption(
			new SwatOption(true, 'accessible'));

		$this->accessibility_selector->addOption(
			new SwatOption(false, 'not accessible'));

		$this->accessibility_selector->display();
	}

	public function process()
	{
		$this->accessibility_selector->process();
		$this->region_flydown->process();
	}

	public function processAction()
	{
		$region = $this->region_flydown->value;

		$accessible = $this->accessibility_selector->value;
		if ($accessible === null)
			return;

		$items = array();
		foreach ($this->items as $id)
			$items[] = $this->db->quote($id, 'integer');

		$id_list = implode(',', $items);

		$where = ($region == 0) ?
			'1 = 1' : 'region = '.$this->db->quote($region, 'integer');

		SwatDB::exec($this->db, sprintf(
			'delete from ArticleRegionBinding where %s and article in (%s)',
			$where, $id_list));

		$regions = ($region == 0) ?
			array_keys($this->regions) : array($region);

		if ($accessible) {
			foreach ($this->items as $item) {
				foreach ($regions as $region) {
					$fields = array('integer:article', 'integer:region');
					$values = array('article' => $item, 'region' => $region);
					SwatDB::insertRow($this->db, 'ArticleRegionBinding',
						$fields, $values);
				}
			}
		}
	}

	public function setItems($items = array())
	{
		$this->items = $items;
	}
}

?>
