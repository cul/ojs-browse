<?php

import('plugins.generic.articleBrowse.BrowseHandler');


class ArticleBrowseHandler extends BrowseHandler {
	
	
	function ArticleBrowseHandler() {
		parent::BrowseHandler();
	}


	function index() {
		$this->browse();
	}

	function browse($alternateQuery = null) {
		parent::setupTemplate($request);
		parent::browse('browse/index.tpl');
	}
	
}

?>
