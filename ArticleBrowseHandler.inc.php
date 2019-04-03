<?php

import('plugins.generic.articleBrowse.BrowseHandler');


class ArticleBrowseHandler extends BrowseHandler {
	
	
	function ArticleBrowseHandler() {
		parent::BrowseHandler();
	}


	function index($request) {
		$this->browse($request);
	}

	function browse($alternateQuery = null) {
		parent::setupTemplate($request);
		parent::browse(BROWSE_INDEX_TEMPLATE);
	}
	
}

?>
