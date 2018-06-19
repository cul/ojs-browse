<?php

/**
 * @file ArticleBrowseSettingsForm.inc.php
 *
 *
 * @package plugins.generic.ArticleBrowse
 * @class ArticleBrowseSettingsForm
 *
 * Form for conference managers to modify Static Page content and title
 * 
 */

import('form.Form');

class ArticleBrowseSettingsForm extends Form {
	/** @var $conferenceId int */
	var $conferenceId;

	/** @var $plugin object */
	var $plugin;

	/** $var $errors string */
	var $errors;

	/**
	 * Constructor
	 * @param $conferenceId int
	 */
	function ArticleBrowseSettingsForm(&$plugin, $conferenceId) {

		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');

		$this->conferenceId = $conferenceId;
		$this->plugin =& $plugin;

		$this->addCheck(new FormValidatorPost($this));
	}


	/**
	 * Initialize form data from current group group.
	 */
	function initData() {
		$conferenceId = $this->conferenceId;
		$plugin =& $this->plugin;

		$rangeInfo =& Handler::getRangeInfo('ArticleBrowse');		
		$ArticleBrowse = $ArticleBrowseDAO->getArticleBrowseByConferenceId($conferenceId);
		$this->setData('ArticleBrowse', $ArticleBrowse);	
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('ArticleBrowse'));
	}

	/**
	 * Save settings/changes
	 */
	function execute() {
		$plugin =& $this->plugin;
		$conferenceId = $this->conferenceId;		
	}

}
?>
