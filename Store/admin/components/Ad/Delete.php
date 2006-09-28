<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';

/**
 * Delete confirmation page for Ads
 *
 * @package   Store
 * @copyright 2006 silverorange
 */
class StoreAdDelete extends AdminDBDelete
{
	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$item_list = $this->getItemList('text');
		
		$sql = sprintf('delete from Ad where id in (%s)', $item_list);

		$num = SwatDB::exec($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(ngettext('One ad has been deleted.',
			'%d ads have been deleted.', $num), $num),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);	
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->title = 'ad';
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'Ad', 'integer:id', null, 'text:title', 'id',
			'id in ('.$item_list.')', AdminDependency::DELETE);

		$dep_orders = new AdminSummaryDependency();
		$dep_orders->title = 'order';
		$dep_orders->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'Orders', 'integer:id', 'integer:ad',
			'ad in ('.$item_list.')', AdminDependency::NODELETE);

		$dep->addDependency($dep_orders);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->switchToCancelButton();
	}

	// }}}
}

?>
