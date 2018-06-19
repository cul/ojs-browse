<?php

import('plugins.generic.articleBrowse.BrowseHandler');



class IndexRecentsHandler extends BrowseHandler {
	
	
	function IndexRecentsHandler() {
		parent::BrowseHandler();
	}

	function index() {
		$this->browse();
	}

	function browse($alternateQuery = null) {
		parent::setupTemplate($request);
		parent::browse('index/journal.tpl');
	}

}

	

?>
