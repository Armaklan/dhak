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

	public function getList() {
		$sql = "SELECT * FROM user WHERE profil = 'Chef' or profil='Assistant'";
		$users = $this->db->fetchAll($sql, array());
		return $users;
	}

	public function getFormations() {
		$sql = "SELECT * FROM formation ORDER BY lvl"; 
		$users = $this->db->fetchAll($sql, array());
		return $users;
	}

	public function getAccessibleUnite($idUser) {
		$sql = "SELECT 
					branche.name as branche_name, 
					groupe.name as groupe_name, 
					district.name as district_name,
					province.name as province_name,
					unite.id as unite_id
				FROM 
				has_access	
				JOIN unite
			    ON has_access.unite_id = unite.id	
				JOIN branche
				ON unite.branche_id = branche.id
				JOIN groupe
				ON unite.groupe_id = groupe.id
				JOIN district 
				ON groupe.district_id = district.id
				JOIN province
				ON district.province_id = province.id
				WHERE has_access.user_id = ?";
		$users = $this->db->fetchAll($sql, array($idUser));
		return $users;
	}

	public function getUserFromRequest($request) {
		$user['longName'] = $request->get("inputName");
		$user['id'] = $request->get("inputId");
		$user['username'] = str_replace(' ', '', $request->get("inputName"));
		$user['mail'] = $request->get("inputEmail");
		$user['tel'] = $request->get("inputTel");
		$user['adresse'] = $request->get("inputAdresse");
		$user['cp'] = $request->get("inputCP");
		$user['city'] = $request->get("inputCity");
		$user['profil'] = $request->get("inputProfil");
		$user['formation_lvl'] = $request->get("inputFormation");
		return $user;
	}

	public function createUser($user) {
		$sql = "INSERT INTO user (username, long_name, mail, city, post_code, adresse, tel, profil, formation_lvl)
			VALUES
			(:username, :longName, :mail, :city, :postCode, :adresse, :tel, :profil, :formation_lvl) ";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("username", $user['username']);
		$stmt->bindValue("longName", $user['longName']);
		$stmt->bindValue("mail", $user['mail']);
		$stmt->bindValue("city", $user['city']);
		$stmt->bindValue("postCode", $user['cp']);
		$stmt->bindValue("adresse", $user['adresse']);
		$stmt->bindValue("tel", $user['tel']);
		$stmt->bindValue("profil", $user['profil']);
		$stmt->bindValue("formation_lvl", $user['formation_lvl']);
		$stmt->execute();

		return $this->db->lastInsertId();
	}

	public function updateUser($user) {
		$sql = "
			UPDATE user
			SET username = :username,
			long_name = :longName,
			mail = :mail,
			city = :city,
			post_code = :postCode,
			adresse = :adresse,
			tel = :tel,
			profil = :profil,
			formation_lvl = :formation_lvl
			WHERE id = :id
			";
			
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id", $user['id']);
		$stmt->bindValue("username", $user['username']);
		$stmt->bindValue("longName", $user['longName']);
		$stmt->bindValue("mail", $user['mail']);
		$stmt->bindValue("city", $user['city']);
		$stmt->bindValue("postCode", $user['cp']);
		$stmt->bindValue("adresse", $user['adresse']);
		$stmt->bindValue("tel", $user['tel']);
		$stmt->bindValue("profil", $user['profil']);
		$stmt->bindValue("formation_lvl", $user['formation_lvl']);
		$stmt->execute();
	}
}
?>
