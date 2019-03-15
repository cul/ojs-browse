<?php

import ('pages.issue.IssueHandler');
import('lib.pkp.classes.db.DBResultRange');
	


class BrowseHandler extends Handler {

	function BrowseHandler() {
		parent::__construct();
		import ('plugins.generic.articleBrowse.ArticleBrowseDAO');
		$browseDao = new ArticleBrowseDAO();
		DAORegistry::registerDAO('ArticleBrowseDAO', $browseDao);
	}

	function setupTemplate($request) {
		parent::setupTemplate($request);
	}


	function browse($templateName){
		$articleDao =& DAORegistry::getDAO('ArticleDAO');
		$browseDao =& DAORegistry::getDAO('ArticleBrowseDAO');
		$sectionDao =& DAORegistry::getDAO('SectionDAO');
		$publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');

		// Add in native OJS functionality for calling article information
		$journal =& Request::getJournal();
		$issueDao =& DAORegistry::getDAO('IssueDAO');
		$templateMgr =& TemplateManager::getManager();
		$issue =& $issueDao->getCurrent($journal->getId(), true);

		// $journal_id = $journal->getId();
		$journal_id = 1;

		$issue =& $issueDao->getCurrent($journal_id, true);



	// 	// Handle the article query
		$page = (isset($_GET['page']) && $_GET['page'] > 1 ) ? (int) $_GET['page'] : 1;
		$itemsPerPage = (isset($_GET['results']) && (int) $_GET['results'] < 50 && (int) $_GET['results'] > 10) ? (int) $_GET['results'] : 10;
		$section =  (isset($_GET['section']) && $validSection = $sectionDao->getById((int) $_GET['section']) ) ? (int) $_GET['section']: null;
		$year = (isset($_GET['year']) && $this->validYear((int) $_GET['year']) ) ? (int) $_GET['year'] : null;  
		$sort = (isset($_GET['sort']) ) ? $_GET['sort'] : null; // TODO: See if this is redundant
		$reverse = (isset($_GET['sort']) && strtolower($_GET['sort']) == 'oldest') ? true : null;
		$pageQuery = ($section?"&section=$section" : '').($year?"&year=$year" : '').($itemsPerPage?"results=$itemsPerPage" : '').($sort?"sort=$sort" : '');

		// Set up some variables for pagination
		$rangeInfo = new DBResultRange($itemsPerPage, $page);
		$itemTotal = $browseDao->getPublishedSubmissionsBrowseCount($journal_id, $year, $section);
		$from = (($rangeInfo->getPage() - 1) * $itemsPerPage) + 1;
		$to = min($itemTotal, $page * $itemsPerPage);
		$pageCount = ceil($itemTotal/$itemsPerPage );
		if ($page >= $pageCount) { // Adjust the range/page if the requested page is greater than the range
			$page = $pageCount;
			$rangeInfo = new DBResultRange($itemsPerPage, $page);
			$from = (($rangeInfo->getPage() - 1) * $itemsPerPage) + 1;
		}

		// Get articles based on the query
		// TODO: Convert this to use a virtualIteratorArray object to be more like other search results
		$publishedArticleObjects = $browseDao->getPublishedSubmissionsBrowse($journal_id, $rangeInfo, $reverse, $year, $section);

		// Handle the generic object returned by getPublishedArticlesByJournalId to get articles
		while ($publishedArticle =& $publishedArticleObjects->next()) {
			$browsePublishedArticles[] =& $publishedArticle;
			unset($publishedArticle);
		}


		// MARK: Attempt at VirtualArrayIterator object
		import('lib.pkp.classes.core.VirtualArrayIterator');
		$browsePublishedArticlesVAI = new VirtualArrayIterator($browsePublishedArticles, count($browsePublishedArticles), $page, $itemsPerPage);
		$browseVai = (!$browsePublishedArticlesVAI) ? 'failure' : "$browsePublishedArticles, ".count($browsePublishedArticles).", $page, $itemsPerPage";
		$templateMgr->assign('browseVai', var_export($browsePublishedArticlesVAI, 1) );

		// TODO: Build and insert no results page

		// Build template
		$templateMgr->assign('pageInfo', $this->mockPageInfo($to, $from, $itemTotal)); // Mock IteratorObject page_info function
		$templateMgr->assign('issue', $issue);
		$templateMgr->assign('browsePublishedArticles', $browsePublishedArticles);
		$templateMgr->assign('totalPublished', $publishedArticleDao->getPublishedArticleCountByJournalId($journal_id));
		// $templateMgr->assign('resultSize', $itemTotal);
		$templateMgr->assign('listStart', $from);
		// $templateMgr->assign('listStop', $to);
		$templateMgr->assign('sections', $this->getSectionsList($articleDao));
		$templateMgr->assign('sectionId', $section);
		$templateMgr->assign('years', $this->getYearsList($browseDao));
		$templateMgr->assign('activeYear', $year);
		$templateMgr->assign('sort', $sort);
#		$templateMgr->assign('numPages', $totalPages);
		//$templateMgr->assign('pagination', $this->pagination($rangeInfo->getPage(), $itemTotal, $itemsPerPage, $pageQuery) );
		$templateMgr->assign('numPageLinks', $itemsPerPage); // For mock IteratorObject pageLinks
		$templateMgr->assign('pageLinks', $this->mockPageLinks($rangeInfo, $pageCount, $itemTotal, $pageQuery) ); 
		// In Press articles
		$inPressArticles =& $browseDao->getArticleInPress();

		$templateMgr->assign('inPressArticles', $inPressArticles);

		$templateMgr->display($templateName);

	}


	/**
	 * Builds pagination for browse pages 
	**/
	function pagination ($currentPage, $itemTotal, $itemsPerPage, $pageQuery = null) {
		$totalPages = ceil($itemTotal/$itemsPerPage );

		for($i = 1; $i <= $totalPages; $i++) {
			$numbers .= ($i == $currentPage) ? 
			'<a class="page active" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page='.$i.'">'.$i.'</a> ' 
			: '<a class="page" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page='.$i.'">'.$i.'</a> ';
		}

		$begin = ($currentPage > 1) ? '<a class="first" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page=1">&lt;&lt;</a> ' : '<a class="void">&lt;&lt;</a> ';

		$end = ($currentPage < $itemTotal) ? '<a class="last" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page=' . $totalPages.'">&gt;&gt;</a> ' : '<a class="void">&gt;&gt;</a> ';
		

		$prev = ($currentPage > 1) ? '<a class="prev" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page=' . ($currentPage-1).'">&lt;</a> ' : '<a class="void">&lt;</a>' ;

		$next = ($currentPage < $itemTotal ) ? '<a class="next" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page='.($currentPage <= 1? 2 : $currentPage+1) .'">&gt;</a> ' : '<a class="void">&gt;</a> ';

		$order = $begin.$prev.$numbers.$next.$end; // TODO: Make this settable via parameter

		$returner = '<div class="pagination">'.$order.'</div>'; 

		return $returner;
	}

	/**
	 * Builds the browse query
	**/
	function queryBuilder() {
		// TODO: Build this function if needed, remove otherwise
		return $pageQuery;
	}
	

	function getYearsList(&$browseDao){
		
		$yearsList[] = 'All Years';
		return $browseDao->getPublishedYearsList($yearsList);
	}

	
	function getSectionsList(&$articleDao){
		
		$sections[] = 'All Sections';
		$browseDao = DAORegistry::getDAO('ArticleBrowseDAO');
		return $browseDao->getSectionNames($sections);
	}


	function validYear($year) {
		$journal =& Request::getJournal();
		$publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
		$issueDao =& DAORegistry::getDAO('IssueDAO');
		$issue =& $issueDao->getCurrent($journal->getId(), true);
		$validYears = $publishedArticleDao->getArticleYearRange($issue->getCurrent());

		$yearFirst = new DateTime($validYears[1]);
		$yearLatest = new DateTime($validYears[0]);

		$dateRequested = new DateTime($year.'-01-01'); // Add more date data, otherwise DateTime returns the current datetime

		if ( $dateRequested->format('Y') >= $yearFirst->format('Y') && $dateRequested->format('Y') <= $yearLatest->format('Y') ) return true;
		return false;
	}

	// Mockup of page_info class available to IteratorObject objects
	function mockPageInfo($to, $from, $itemTotal){
		return AppLocale::translate('navigation.items', array(
			'from' => ($to===0?0:$from),
			'to' => $to,
			'total' => $itemTotal
			));
	}

	// Mockup of page_links class available to IteratorObject objects
	function mockPageLinks($rangeInfo, $pageCount, $itemTotal, $pageQuery) {
		$templateMgr =& TemplateManager::getManager();
		$itemsPerPage = $templateMgr->get_template_vars('numPageLinks');
		if (!is_numeric($itemsPerPage)) $itemsPerPage=10;

		$page = $rangeInfo->getPage();

		$pageBase = max($page - floor($itemsPerPage / 2), 1);
#		$paramName = $name . 'Page';

		if ($pageCount<=1) return '';

		$value = '';

		if ($page>1) {
			$value .= '<a href="' . Request::url(null, 'browse') . '?' . $pageQuery . '&page=' . 1 . '">&lt;&lt;</a>&nbsp;';
			$value .= '<a href="' . Request::url(null, 'browse') . '?' . $pageQuery . '&page=' . ($page-1).'">&lt;</a>&nbsp;';			
		}

		for ($i=$pageBase; $i<min($pageBase+$itemsPerPage, $pageCount+1); $i++) {
			if ($i == $page) {
				$value .= "<strong>$i</strong>&nbsp;";
			} else {
				$value .=  '<a class="page" href="'.Request::url(null, 'browse').'?'.$pageQuery.'&page='.$i.'">'.$i.'</a>&nbsp;';
			}
		}

		if ($page < $pageCount) {
			$value .= '<a href="' . Request::url(null, 'browse') . '?' . $pageQuery . '&page=' . ($page+1) . '">&gt;</a>&nbsp;';
			$value .= '<a href="' . Request::url(null, 'browse') . '?' . $pageQuery . '&page=' . ($pageCount) . '">&gt;&gt;</a>&nbsp;';
		}

		return $value;

	}
	
}

?>
