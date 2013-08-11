<?php
/**
 * User entity managment
 *
 * @package userService
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license BSD
 */

class UserService {

	private $db;
	private $session;
    private $log;

	public function __construct($db, $session, $log)
    {
        $this->db = $db;
        $this->session = $session;
        $this->log = $log;
    }

	public function login($username, $password) {
		$sql = "SELECT * FROM user WHERE username = ?";
	    $user = $this->db->fetchAssoc($sql, array($username));

	    if($user == null)  {
	    	throw new Exception("Nom d'utilisateur incorrect");
	    }
		if($user['password'] != md5($password)) {
	    	throw new Exception('Mots de passe incorrect');
	    }
	    $this->session->set('user', array('id' => $user['id'], 'username' => $username));
	}

	public function logout() {
		$this->session->set('user', null);
	}

	public function getCurrentUser() {
		$sessionUser = $this->session->get('user');
		if ($sessionUser == null) {
			throw new Exception('Non authentifié');
		} else {
			$username = $sessionUser['username'];
			$sql = "SELECT * FROM user WHERE username = ?";
	    	$user = $this->db->fetchAssoc($sql, array($username));
	    	return $user;
		}
	}

	public function changePassword($request) {

		if($request->get('password') != $request->get('password2')) {
			throw new Exception("Les mots de passes ne correspondent pas");
		}

		$sql = "UPDATE user
				SET
					password = :password
				WHERE username = :username";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("username", $this->session->get('user')['login']);
		$stmt->bindValue("password", md5($request->get('password')));
		$stmt->execute();
	}

	public function getByUsername($username) {
		$sql = "SELECT user.*
                    FROM user
                    WHERE username = ?";
	    $user = $this->db->fetchAssoc($sql, array($username));
	    return $user;
	}

        public function getById($id) {
		$sql = "SELECT * FROM user WHERE id = ?";
	    $user = $this->db->fetchAssoc($sql, array($id));
	    return $user;
	}


}
?>
