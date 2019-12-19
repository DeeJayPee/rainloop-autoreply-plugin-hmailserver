<?php

class HmailserverAutoReplyPlugin extends \RainLoop\Plugins\AbstractPlugin
{
	/**
	 * @return void
	 */
	public function Init()
	{
		$this->UseLangs(true); // start use langs folder

		$this->addJs('js/HmailserverAutoReply.js'); // add js file

		$this->addAjaxHook('AjaxGetCustomUserData', 'AjaxGetCustomUserData');
		$this->addAjaxHook('AjaxSaveCustomUserData', 'AjaxSaveCustomUserData');

		$this->addTemplate('templates/PluginHmailserverAutoReplyTab.html');
	}

	/**
	* @return array
	*/
	public function configMapping()
	{
		return array(
		\RainLoop\Plugins\Property::NewInstance('mHost')->SetLabel('MySQL Host')
			->SetDefaultValue('127.0.0.1'),
		\RainLoop\Plugins\Property::NewInstance('mUser')->SetLabel('MySQL User'),
		\RainLoop\Plugins\Property::NewInstance('mPass')->SetLabel('MySQL Password')
			->SetType(\RainLoop\Enumerations\PluginPropertyType::PASSWORD),
		\RainLoop\Plugins\Property::NewInstance('mDatabase')->SetLabel('MySQL Database'),
		\RainLoop\Plugins\Property::NewInstance('mTable')->SetLabel('MySQL Table'));
	}

	/**
	 * @return array
	 */
	public function AjaxGetCustomUserData()
	{
		include_once __DIR__.'/HmailServerAutoReplySqlDriver.php';

		$oAccount = $this->Manager()->Actions()->GetAccount();
		if ($oAccount)
		{
			$oProvider = new HmailServerAutoReplySqlDriver();
			$oProvider
			->SetLogger($this->Manager()->Actions()->Logger())
			->SetmHost($this->Config()->Get('plugin', 'mHost', ''))
			->SetmUser($this->Config()->Get('plugin', 'mUser', ''))
			->SetmPass($this->Config()->Get('plugin', 'mPass', ''))
			->SetmDatabase($this->Config()->Get('plugin', 'mDatabase', ''))
			->SetmTable($this->Config()->Get('plugin', 'mTable', 'hm_accounts'))
			->SetmSql('SELECT accountvacationmessageon, accountvacationsubject, accountvacationmessage, accountvacationexpires, accountvacationexpiredate FROM :table WHERE accountaddress = :email');
			$values = $oProvider->Get($oAccount)[0];
		} else {
			die;
		}

		return $this->ajaxResponse(__FUNCTION__, array(
			'vacationmessageon' => $values['accountvacationmessageon'],
			'vacationsubject' => $values['accountvacationsubject'],
			'vacationmessage' => $values['accountvacationmessage'],
			'vacationmessageexpires' => $values['accountvacationexpires'],
			'vacationmessageexpiresdate' => date('Y-m-d', strtotime($values['accountvacationexpiredate'])),
		));
	}

	/**
	 * @return array
	 */
	public function AjaxSaveCustomUserData()
	{
		include_once __DIR__.'/HmailServerAutoReplySqlDriver.php';

		$vacationmessageon = ($this->ajaxParam('vacationmessageon') == '' || $this->ajaxParam('vacationmessageon') == 'false')?0:1;
		$vacationsubject = $this->ajaxParam('vacationsubject');
		$vacationmessage = $this->ajaxParam('vacationmessage');
		$vacationmessageexpires = ($this->ajaxParam('vacationmessageexpires') == '' || $this->ajaxParam('vacationmessageexpires') == 'false')?0:1;
		$vacationmessageexpiresdate = ($this->ajaxParam('vacationmessageexpiresdate'))?$this->ajaxParam('vacationmessageexpiresdate'):date('Y-m-d');

		$oAccount = $this->Manager()->Actions()->GetAccount();
		if ($oAccount)
		{
			$oProvider = new HmailServerAutoReplySqlDriver();
			$oProvider
			->SetLogger($this->Manager()->Actions()->Logger())
			->SetmHost($this->Config()->Get('plugin', 'mHost', ''))
			->SetmUser($this->Config()->Get('plugin', 'mUser', ''))
			->SetmPass($this->Config()->Get('plugin', 'mPass', ''))
			->SetmDatabase($this->Config()->Get('plugin', 'mDatabase', ''))
			->SetmTable($this->Config()->Get('plugin', 'mTable', 'hm_accounts'))
			->SetmSql('UPDATE :table SET accountvacationmessageon = :accountvacationmessageon, accountvacationsubject = :accountvacationsubject, accountvacationmessage = :accountvacationmessage, accountvacationexpires = :accountvacationexpires, accountvacationexpiredate = :accountvacationexpiredate WHERE accountaddress = :email');
			$values = $oProvider->Set($oAccount, $vacationmessageon, $vacationsubject, $vacationmessage, $vacationmessageexpires, $vacationmessageexpiresdate);
		} else {
			die;
		}

		return $this->ajaxResponse(__FUNCTION__, true);
	}

}
