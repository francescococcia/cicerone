<?php

namespace Core;

/**
 * Unique random tokens
 *
 * PHP version 7.0
 */

class Token
{
	protected $token;

	public function __construct($token_value = null)
	{
		if ($token_value)
		{
			$this->token = $token_value;
		}
		else
		{
			$this->token = bin2hex(random_bytes(16));
		}
	}

	public function getToken()
	{
		return $this->token;
	}

	public function getHash()
	{
		return hash_hmac('sha256',$this->token, \Config\Config::SECRET_KEY);
	}
}
