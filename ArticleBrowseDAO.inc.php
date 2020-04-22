<?php

/**
 *
 * @class ArticleBrowseDAO
 */

// $Id$



define('STATUS_ARCHIVED', 0);


class ArticleBrowseDAO extends DAO {
	var $submissionDao;
	var $authorDao;
	var $galleyDao;
	var $suppFileDao;

	var $articleCache;
	var $submissionsInSectionsCache;

	
 	/**
	 * Constructor.
	 */
 	function ArticleBrowseDAO() {
 		parent::__construct();
 		$this->submissionDao =& Application::getSubmissionDAO();
 		$this->authorDao =& DAORegistry::getDAO('AuthorDAO');
 		$this->galleyDao =& DAORegistry::getDAO('ArticleGalleyDAO');
		$this->suppFileDao =& DAORegistry::getDAO('SubmissionFileDAO');
		$this->publishedArticleDao =& DAORegistry::getDAO('PublishedArticleDAO');
 	}



	
	function getSectionNames($sections = array(), $journal_id) {
	
		$sql ='select ss.section_id as section_id, ss.setting_value as setting_title from section_settings ss, sections s where ss.setting_name = "title" and s.journal_id='.$journal_id.' and s.section_id=ss.section_id and s.section_id in (select distinct(section_id) from submissions where submission_id in (select submission_id from submission_settings)) group by setting_value';

		$result =& $this->retrieve($sql);

		if ($result->RecordCount() != 0) {
	
			for ($i=1; !$result->EOF; $i++) {
			
				$row = $result->GetRowAssoc(false);
			
				$sectionId = $row['section_id'];
				$sectionTitle = $row['setting_title'];
				
				$sections[$sectionId] = $sectionTitle;

				$result->moveNext();
			}
		}
	
		$result->Close();
		unset($result);
	
		return $sections;
	}


	/**
	 * Retrieve Published submissions by year and journalID
	 * @param $year int
	 * @param $journalId int
	 * @return object array
	 */
	function &getPublishedSubmissionsBrowse($journalId, $rangeInfo = null, $reverse = false, $year = null, $sectionId = null) {
		$primaryLocale = AppLocale::getPrimaryLocale();
		$locale = AppLocale::getLocale();
		$params = array(
			'title',
			$primaryLocale,
			'title',
			$locale,
			'abbrev',
			$primaryLocale,
			'abbrev',
			$locale
			);
		if ($journalId !== null) $params[] = (int) $journalId;
		if ($sectionId !== null) $params[] = (int) $sectionId;
		$result =& $this->retrieveRange(
			'SELECT	pa.*,
			a.*,
			COALESCE(stl.setting_value, stpl.setting_value) AS section_title,
			COALESCE(sal.setting_value, sapl.setting_value) AS section_abbrev
			FROM	published_submissions pa
			LEFT JOIN submissions a ON pa.submission_id = a.submission_id
			LEFT JOIN issues i ON pa.issue_id = i.issue_id
			LEFT JOIN sections s ON s.section_id = a.section_id
			LEFT JOIN section_settings stpl ON (s.section_id = stpl.section_id AND stpl.setting_name = ? AND stpl.locale = ?)
			LEFT JOIN section_settings stl ON (s.section_id = stl.section_id AND stl.setting_name = ? AND stl.locale = ?)
			LEFT JOIN section_settings sapl ON (s.section_id = sapl.section_id AND sapl.setting_name = ? AND sapl.locale = ?)
			LEFT JOIN section_settings sal ON (s.section_id = sal.section_id AND sal.setting_name = ? AND sal.locale = ?)
			WHERE '.($year?'YEAR(pa.date_published) = '.$year.' AND':'').' i.published = 1
			' . ($journalId !== null?'AND a.context_id = ?':'') . '
			' . ($sectionId !== null?'AND a.section_id = ?':'') . '
			AND a.status <> ' . STATUS_ARCHIVED . '
			ORDER BY date_published ' . ($reverse?'ASC':'DESC'),
			$params,
			$rangeInfo
			);
$returner = new DAOResultFactory($result, $this, '_returnPublishedArticleFromRow');

return $returner;
}

	/**
	 * Retrieve Published submissions by year and journalID
	 * @param $year int
	 * @param $journalId int
	 * @return object array
	 */
	function &getLatestPublishedsubmissions($limit = 5, $journalId = null, $includeSectionId = null, $excludeSectionId = null) {

		// If there is an mandatory section in the 5 most recent publications:
		// Display the five most recent publications in reverse-chron. order. 

		// If there is no mandatory section in the 5 most recent publications:
		// Display the four most recent publications in reverse-chron. order, 
		// followed by the most recent "article" type

		$primaryLocale = AppLocale::getPrimaryLocale();
		$locale = AppLocale::getLocale();
		$params = array(
			'title',
			$primaryLocale,
			'title',
			$locale,
			'abbrev',
			$primaryLocale,
			'abbrev',
			$locale
			);
		if ($journalId !== null) $params[] = (int) $journalId;
		if ($includeSectionId !== null) {
			$params[] = (int) $includeSectionId;
			$limit = $limit - 1; // Take one away to account for the required included section

			array_push( 
				$params,
				'title',
				$primaryLocale,
				'title',
				$locale,
				'abbrev',
				$primaryLocale,
				'abbrev',
				$locale
				);

			if ($journalId !== null) $params[] = (int) $journalId;
		}
		
		if ($excludeSectionId !== null) $params[]  = (int) $excludeSectionId;
		if ($limit !== null) $params[] = $limit;

		$includeSectionQuery = 'SELECT	pa.*,
		a.*,
		COALESCE(stl.setting_value, stpl.setting_value) AS section_title,
		COALESCE(sal.setting_value, sapl.setting_value) AS section_abbrev
		FROM	published_submissions pa
		LEFT JOIN submissions a ON pa.submission_id = a.submission_id
		LEFT JOIN issues i ON pa.issue_id = i.issue_id
		LEFT JOIN sections s ON s.section_id = a.section_id
		LEFT JOIN section_settings stpl ON (s.section_id = stpl.section_id AND stpl.setting_name = ? AND stpl.locale = ?)
		LEFT JOIN section_settings stl ON (s.section_id = stl.section_id AND stl.setting_name = ? AND stl.locale = ?)
		LEFT JOIN section_settings sapl ON (s.section_id = sapl.section_id AND sapl.setting_name = ? AND sapl.locale = ?)
		LEFT JOIN section_settings sal ON (s.section_id = sal.section_id AND sal.setting_name = ? AND sal.locale = ?)
		WHERE i.published = 1
		' . ($journalId !== null?'AND a.context_id = ?':'') . '
		AND a.section_id = ?
		AND a.status <> ' . STATUS_ARCHIVED . '
		ORDER BY date_published DESC
		LIMIT '.$limit;

		$latestPublishedQuery = 'SELECT	pa.*,
		a.*,
		COALESCE(stl.setting_value, stpl.setting_value) AS section_title,
		COALESCE(sal.setting_value, sapl.setting_value) AS section_abbrev
		FROM	published_submissions pa
		LEFT JOIN submissions a ON pa.submission_id = a.submission_id
		LEFT JOIN issues i ON pa.issue_id = i.issue_id
		LEFT JOIN sections s ON s.section_id = a.section_id
		LEFT JOIN section_settings stpl ON (s.section_id = stpl.section_id AND stpl.setting_name = ? AND stpl.locale = ?)
		LEFT JOIN section_settings stl ON (s.section_id = stl.section_id AND stl.setting_name = ? AND stl.locale = ?)
		LEFT JOIN section_settings sapl ON (s.section_id = sapl.section_id AND sapl.setting_name = ? AND sapl.locale = ?)
		LEFT JOIN section_settings sal ON (s.section_id = sal.section_id AND sal.setting_name = ? AND sal.locale = ?)
		WHERE i.published = 1
		' . ($journalId !== null?'AND a.context_id = ?':'') . '
		' . ($excludeSectionId !== null?'AND a.section_id != ?':'') . '
		AND a.status <> ' . STATUS_ARCHIVED . '
		ORDER BY date_published DESC
		LIMIT ?';

		$limit++;
		$query = ($includeSectionId !== null) ? "($includeSectionQuery) UNION DISTINCT ($latestPublishedQuery) ORDER BY date_published DESC LIMIT $limit" : $latestPublishedQuery;

		$result =& $this->retrieveRange(
			$query,
			$params,
			null
			);

		$returner = new DAOResultFactory($result, $this, '_returnPublishedArticleFromRow');
		return $returner;
	}

	/**
	 * Retrieve Published submissions by year and journalID
	 * @param $year int
	 * @param $journalId int
	 * @return object array
	 */
	function &getPublishedSubmissionsBrowseCount($journalId, $year = null, $sectionId = null) {

		$primaryLocale = AppLocale::getPrimaryLocale();
		$locale = AppLocale::getLocale();
		$params = array(
			'title',
			$primaryLocale,
			'title',
			$locale,
			'abbrev',
			$primaryLocale,
			'abbrev',
			$locale
			);
		if ($journalId !== null) $params[] = (int) $journalId;
		if ($sectionId !== null) $params[] = (int) $sectionId;
		$sql_select = 'SELECT COUNT(*) FROM published_submissions pa LEFT JOIN submissions a ON pa.submission_id = a.submission_id LEFT JOIN issues i ON pa.issue_id = i.issue_id LEFT JOIN sections s ON s.section_id = a.section_id LEFT JOIN section_settings stpl ON (s.section_id = stpl.section_id AND stpl.setting_name = ? AND stpl.locale = ?) LEFT JOIN section_settings stl ON (s.section_id = stl.section_id AND stl.setting_name = ? AND stl.locale = ?) LEFT JOIN section_settings sapl ON (s.section_id = sapl.section_id AND sapl.setting_name = ? AND sapl.locale = ?) LEFT JOIN section_settings sal ON (s.section_id = sal.section_id AND sal.setting_name = ? AND sal.locale = ?) WHERE '.($year?'YEAR(pa.date_published) = '.$year.' AND':'').' i.published = 1' . ($journalId !== null?' AND a.context_id = ?':'') . '' . ($sectionId !== null?' AND a.section_id = ?':'') . ' AND a.status <> ' . STATUS_ARCHIVED;
		$result =& $this->retrieveRange($sql_select, $params, null);

		return (int) preg_replace('/[^0-9]+/', '', $result);
	}

	/**
	 * creates and returns a published article object from a row, including all supp files etc.
	 * @param $row array
	 * @param $callHooks boolean Whether or not to call hooks
	 * @return PublishedArticle object
	 */
	function &_returnPublishedArticleFromRow($row, $callHooks = true) {

		if ($callHooks) HookRegistry::call('PublishedArticleDAO::_returnPublishedArticleFromRow', array(&$publishedArticle, &$row));
		return $this->publishedArticleDao->_fromRow($row);
		// return $this->publishedArticleDao->getPublishedArticleByArticleId($row['submission_id']);			
		return $publishedArticle;
	}
	
	/**
	 * Get the submissions in press
	 * 
	 * Created by Kate Park
	 */
	function &getArticleInPress() {

	 	// Return the list of reviewers inputted.
		$result = mysql_query('select submission_id from submissions where status='.STATUS_QUEUED.' and (stage_id='.WORKFLOW_STAGE_ID_EDITING.' or stage_id='.WORKFLOW_STAGE_ID_PRODUCTION.')');

		$submissions = array();

		$i = 0;
		while ($row = mysql_fetch_row($result)) {

			$id = $row[0];
			$submissions[$i] = $this->submissionDao->getById($id, null, false);
			$i++;
		}
		$returner = $submissions;

		return $returner;
	}

/*
	 * Duplicate functionality of PublishedArticleDAO::getArticleYearRange()
	*/
	function getPublishedYearsList($yearsList, $journal_id){
		
		$sql = 'SELECT 
				DISTINCT EXTRACT(YEAR FROM pa.date_published) as year
				FROM published_submissions pa, submissions a
				WHERE	pa.submission_id = a.submission_id
				AND a.status <> "0"  and a.context_id='.$journal_id.'
				ORDER BY pa.date_published DESC';
		
		$result =& $this->retrieve($sql);

		if ($result->RecordCount() != 0) {
		
			for ($i=1; !$result->EOF; $i++) {
		
				$row = $result->GetRowAssoc(false);
				$yearsList[] = $row['year'];
		
				$result->moveNext();
			}
		}
		
		$result->Close();
		unset($result);
		
		return $yearsList;
	}
	

}

?>
