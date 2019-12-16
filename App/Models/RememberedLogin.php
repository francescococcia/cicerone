<?php

namespace App\Models;

use PDO;
use \Core\Token;
use \App\Models\UserSignedIn;

class RememberedLogin extends \Core\Model
{
	public static function findByToken($token)
	{
		$token = new Token($token);
		$token_hash = $token->getHash();

		$sql = 'SELECT * FROM remembered_login
				WHERE token_hash = :token_hash';

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

		$stmt->execute();

		return $stmt->fetch();
	}

	public function getUser()
	{
		return UserSignedIn::findByID($this->user_id);
	}

	public function hasExpired()
	{
		return strtotime($this->expires_at) < time();
	}

	public function delete()
	{
		$sql = 'DELETE FROM remembered_login
				WHERE token_hash = :token_hash';

		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

		$stmt->execute();
	}
}
