<?php
/**
 * @file ArticleBrowsePlugin.inc.php
 *
 *
 * @package cdrs.articlebrowse
 *
 * @class ArticleBrowsePlugin
 *
 * @brief Handle requests for about the conference functions. 
 *
 * @defgroup pages_articlebrowse
 * @ingroup pages_articlebrowse
 *
 * ArticleBrowsePlugin class
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');
import('lib.pkp.classes.db.DBResultRange');


class ArticleBrowsePlugin extends GenericPlugin {

	function getName() {
		return 'ArticleBrowsePlugin';
	}

	function getDisplayName() {
		return _('cdrs.articleBrowse.displayName');
	}

	function getDescription() {
		$description = _('cdrs.articleBrowse.description'."<br />");
		return $description;
	}

	function isTinyMCEInstalled() {
		$tinyMCEPlugin =& PluginRegistry::getPlugin('generic', 'TinyMCEPlugin');

		if ( $tinyMCEPlugin )
			return $tinyMCEPlugin->getEnabled();

		return false;
	}

	/**
	 * Register the plugin, attaching to hooks as necessary.
	 * @param $category string
	 * @param $path string
	 * @return boolean
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			if ($this->getEnabled()) {
				HookRegistry::register('LoadHandler', array(&$this, 'callbackHandleContent'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Declare the handler function to process the actual page PATH
	 */
	function callbackHandleContent($hookName, $args) {
		$templateMgr =& TemplateManager::getManager();

		$page =& $args[0];
		$op =& $args[1];

		if ( $page == 'browse') {
			define('HANDLER_CLASS', 'ArticleBrowseHandler');
			$this->import('ArticleBrowseHandler');
			return true;
		}elseif ($page == '') {
			define('HANDLER_CLASS', 'IndexRecentsHandler');
			$this->import('IndexRecentsHandler');
			return true;
		}
		return false;
	}

	/**
	 * Determine whether or not this plugin is enabled.
	 */
	function getEnabled() {
		$conference =& Request::getJournal();
		$conferenceId = $conference?$conference->getId():0;
		return $this->getSetting($conferenceId, 'enabled');
	}

	/**
	 * Set the enabled/disabled state of this plugin
	 */
	function setEnabled($enabled) {
		$conference =& Request::getJournal();
		$conferenceId = $conference?$conference->getId():0;
		$this->updateSetting($conferenceId, 'enabled', $enabled, bool);

		return true;
	}

	/**
	 * Display verbs for the management interface.
	 */
	
	function getManagementVerbs() {
		$verbs = array();
		if ($this->getEnabled()) {
			$verbs[] = array(
				'disable',
				'manager.plugins.disable'
			);
		} else {
			$verbs[] = array(
				'enable',
				'manager.plugins.enable'
			);
		}
		return $verbs;
	}
	

	/**
	 * Perform management functions
	 */

	function manage($verb, $args) {
		$returner = true;

		$templateMgr =& TemplateManager::getManager();
		$templateMgr->register_function('plugin_url', array(&$this, 'smartyPluginUrl'));
		// MARK: Is this needed? >> $templateMgr->assign('pagesPath', Request::url(null, null, 'articlebrowse', 'view', 'REPLACEME'));

		$pageCrumbs = array(
			array(
				Request::url(null, null, 'user'),
				'navigation.user'
			),
			array(
				Request::url(null, null, 'manager'),
				'user.role.manager'
			)
		);

		switch ($verb) {
			case 'settings':
				$conference =& Request::getConference();

				$this->import('ArticleBrowseSettingsForm');
				$form = new ArticleBrowseSettingsForm($this, $conference->getId());

				$templateMgr->assign('pageHierarchy', $pageCrumbs);
				$form->initData();
				$form->display();
				break;
			case 'enable':
				$this->setEnabled(true);
				$returner = false;
				break;
			case 'disable':
				$this->setEnabled(false);
				$returner = false;
				break;
		}

		return $returner;
	}
	

}
?>