<?php

namespace Core;

class Flash
{
	private const SUCCESS = 'success';

	private const INFO = 'info';

	private const WARNING = 'warning';

	public static function addMessage($message, $type = 'success')
	{
		if(!isset($_SESSION['flash_notifications']))
		{
			$_SESSION['flash_notifications'] = [];
		}
		$_SESSION['flash_notifications'][] = [
			'body'=> $message,
			'type'=> $type
		];
	}

	public static function getMessages()
	{
		if(isset($_SESSION['flash_notifications']))
		{
			$messages = $_SESSION['flash_notifications'];
			unset($_SESSION['flash_notifications']);
			return $messages;
		}
	}

	public static function getWarningMessage()
	{
		return constant("self::WARNING");
	}

	public static function getInfoMessage()
	{
		return constant("self::INFO");
	}
}
