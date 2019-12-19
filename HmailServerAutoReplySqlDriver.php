<?php

class HmailServerAutoReplySqlDriver
{
	/**
	 * @var string
	 */
	private $mHost = '127.0.0.1';

	/**
	 * @var string
	 */
	private $mUser = '';

	/**
	 * @var string
	 */
	private $mPass = '';

	/**
	 * @var string
	 */
	private $mDatabase = '';

	/**
	 * @var string
	 */
	private $mTable = '';

	/**
	 * @var string
	 */
	private $mSql = '';

	/**
	 * @var \MailSo\Log\Logger
	 */
	private $oLogger = null;

	/**
	 * @param string $mHost
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetmHost($mHost)
	{
		$this->mHost = $mHost;
		return $this;
	}

	/**
	 * @param string $mUser
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetmUser($mUser)
	{
		$this->mUser = $mUser;
		return $this;
	}

	/**
	 * @param string $mPass
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetmPass($mPass)
	{
		$this->mPass = $mPass;
		return $this;
	}

	/**
	 * @param string $mDatabase
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetmDatabase($mDatabase)
	{
		$this->mDatabase = $mDatabase;
		return $this;
	}

	/**
	 * @param string $mTable
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetmTable($mTable)
	{
		$this->mTable = $mTable;
		return $this;
	}

	/**
	 * @param string $mSql
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetmSql($mSql)
	{
		$this->mSql = $mSql;
		return $this;
	}

	/**
	 * @param \MailSo\Log\Logger $oLogger
	 *
	 * @return \HmailServerAutoReplySqlDriver
	 */
	public function SetLogger($oLogger)
	{
		if ($oLogger instanceof \MailSo\Log\Logger)
		{
			$this->oLogger = $oLogger;
		}

		return $this;
	}

	/**
	 * @param \RainLoop\Account $oAccount
	 *
	 * @return bool
	 */
	public function PasswordChangePossibility($oAccount)
	{
		return $oAccount && $oAccount->Email();
	}

	/**
	 * @param \RainLoop\Account $oAccount
	 *
	 * @return array
	 */
	public function Get(\RainLoop\Account $oAccount)
	{
		if ($this->oLogger)
		{
			$this->oLogger->Write('Try to get auto-reply for '.$oAccount->Email());
		}

		$bResult = false;

		$dsn = 'mysql:host='.$this->mHost.';dbname='.$this->mDatabase.';charset=utf8';
		$options = array(
			PDO::ATTR_EMULATE_PREPARES  => false,
			PDO::ATTR_PERSISTENT        => true,
			PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION
		);

		try
		{
			$conn = new PDO($dsn, $this->mUser, $this->mPass, $options);

			$sEmail = $oAccount->Email();

			$old = array(':table' );
			$new = array( $this->mTable);

			$this->mSql = str_replace($old, $new, $this->mSql);

			$select = $conn->prepare($this->mSql);
			$mSqlReturn = $select->execute(array('email' => $sEmail));

			if ($mSqlReturn == true)
			{
				$bResult = $select->fetchAll();
				if ($this->oLogger)
				{
					$this->oLogger->Write('Success! Autoreply retrieved.');
				}
			}
			else
			{
				$bResult = false;
				if ($this->oLogger)
				{
					$this->oLogger->Write('Something went wrong.');
				}
			}
		}
		catch (\Exception $oException)
		{
			$bResult = false;
			if ($this->oLogger)
			{
				$this->oLogger->WriteException($oException);
			}
		}

		return $bResult;
	}


	/**
	 * @param \RainLoop\Account $oAccount
	 * @param string $vacationmessageon
	 * @param string $vacationsubject
	 * @param string $vacationmessage
	 * @param string $vacationmessageexpires
	 * @param string $vacationmessageexpiresdate
	 *
	 * @return bool
	 */
	public function Set(\RainLoop\Account $oAccount, $vacationmessageon, $vacationsubject, $vacationmessage, $vacationmessageexpires, $vacationmessageexpiresdate)
	{
		if ($this->oLogger)
		{
			$this->oLogger->Write('Try to set auto-reply for '.$oAccount->Email());
		}

		$bResult = false;

		$dsn = 'mysql:host='.$this->mHost.';dbname='.$this->mDatabase.';charset=utf8';
		$options = array(
			PDO::ATTR_EMULATE_PREPARES  => false,
			PDO::ATTR_PERSISTENT        => true,
			PDO::ATTR_ERRMODE           => PDO::ERRMODE_EXCEPTION
		);

		try
		{
			$conn = new PDO($dsn, $this->mUser, $this->mPass, $options);

			//prepare SQL varaibles
			$sEmail = $oAccount->Email();

			//simple check

			$old = array(':table');
			$new = array($this->mTable);

			$this->mSql = str_replace($old, $new, $this->mSql);

			$select = $conn->prepare($this->mSql);
			$mSqlReturn = $select->execute(array('email' => $sEmail, 'accountvacationmessageon' => $vacationmessageon, 'accountvacationsubject' => $vacationsubject, 'accountvacationmessage' => $vacationmessage, 'accountvacationexpires' => $vacationmessageexpires, 'accountvacationexpiredate' => $vacationmessageexpiresdate));
			//accountvacationmessageon = :accountvacationmessageon, accountvacationsubject = :accountvacationsubject, accountvacationmessage = :accountvacationmessage, accountvacationexpires = :accountvacationexpires, accountvacationexpiredate = :accountvacationexpiredate WHERE accountaddress = :email

			if ($mSqlReturn == true)
			{
				$bResult = true;
				if ($this->oLogger)
				{
					$this->oLogger->Write('Success! Autoreply set.');
				}
			}
			else
			{
				$bResult = false;
				if ($this->oLogger)
				{
					$this->oLogger->Write('Something went wrong.');
				}
			}
		}
		catch (\Exception $oException)
		{
			$bResult = false;
			if ($this->oLogger)
			{
				$this->oLogger->WriteException($oException);
			}
		}

		return $bResult;
	}
}
