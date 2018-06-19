<?php

/**
 * 
 */

/**
 * 
 *
 *
 * @class ArticleBrowse
 *
 */



import('lib.pkp.classes.submission.Submission');

class ArticleBrowse  extends Submission {
	/**
	 * Constructor.
	 */
	function ArticleBrowse() {
		parent::Submission();
	}

	function buildPublishedArticlesArray($n)
	{
		if($val instanceof PublishedArticle)
			return 'set';
		else
			return 'notSet';
	}


	function convertSearchToPublishedArticlesArray($virtualArrayIterator){

		$map = array_map($virtualArrayIterator, 'buildPublishedArticlesArray');
		return $map;
	}

	
}

?>
