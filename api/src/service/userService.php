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
	    $this->session->set('user', array('id' => $user['id'], 'username' => $username, 'profil' => $user['profil']));
	}

	public function logout() {
		$this->session->set('user', '');
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

	public function all() {
		$sql = "SELECT * FROM user";
		$users = $this->db->fetchAll($sql, array());
		return $users;
	}

	public function getList() {
		$sql = "SELECT * FROM user 
			LEFT JOIN asso_unite_user
			ON user.id = asso_unite_user.user_id
			WHERE ( profil = 'Chef' 
			or profil='Assistant' )
			AND asso_unite_user.user_id IS NULL";
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
					unite.id as unite_id,
					count(asso.user_id) as nb_chef,
					unite.size as size,
					unite.nb_sizaine as nb_sizaine
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
				LEFT JOIN asso_unite_user asso
				ON asso.unite_id = unite.id
				WHERE has_access.user_id = ?
				GROUP BY 
				branche_name, 
				groupe_name, 
				district_name,
				province_name";
		$users = $this->db->fetchAll($sql, array($idUser));
		return $users;
	}

	public function getAllUnite() {
		$sql = "SELECT DISTINCT
					branche.name as branche_name, 
					groupe.name as groupe_name, 
					district.name as district_name,
					province.name as province_name,
					unite.id as unite_id
				FROM 
				unite
				JOIN branche
				ON unite.branche_id = branche.id
				JOIN groupe
				ON unite.groupe_id = groupe.id
				JOIN district 
				ON groupe.district_id = district.id
				JOIN province
				ON district.province_id = province.id
				";
		$users = $this->db->fetchAll($sql, array());
		return $users;
	}

	public function getProfils() {
		$sql = "SELECT 
					*
				FROM 
				user_profil
				";
		$profils = $this->db->fetchAll($sql, array());
		return $profils;
	}


	public function getUserFromRequest($request) {
		$user['longName'] = $request->get("inputName");
		$user['firstname'] = $request->get("inputFirstName");
		$user['birthday'] = $request->get("inputBirthday");
		$user['id'] = $request->get("inputId");
		$user['username'] = str_replace(' ', '', $request->get("inputName"));
		$user['password'] = "";
		$user['mail'] = $request->get("inputEmail");
		$user['tel'] = $request->get("inputTel");
		$user['adresse'] = $request->get("inputAdresse");
		$user['cp'] = $request->get("inputCP");
		$user['commentaire'] = $request->get("inputCommentaire");
		$user['city'] = $request->get("inputCity");
		$user['profil'] = $request->get("inputProfil");
		$user['formation_lvl'] = $request->get("inputFormation");
		return $user;
	}

	public function getUserAllFromRequest($request) {
		$user['longName'] = $request->get("inputName");
		$user['firstname'] = $request->get("inputFirstName");
		$user['birthday'] = $request->get("inputBirthday");
		$user['id'] = $request->get("inputId");
		$user['username'] = $request->get("inputUsername");
		$user['password'] = md5($request->get("inputPassword"));
		$user['mail'] = $request->get("inputEmail");
		$user['tel'] = $request->get("inputTel");
		$user['adresse'] = $request->get("inputAdresse");
		$user['cp'] = $request->get("inputCP");
		$user['commentaire'] = $request->get("inputCommentaire");
		$user['city'] = $request->get("inputCity");
		$user['profil'] = $request->get("inputProfil");
		$user['formation_lvl'] = $request->get("inputFormation");
		return $user;
	}

	public function createUser($user) {
		$sql = "INSERT INTO user (username, password, long_name, firstname, birthday, mail, city, 
			post_code, adresse, tel, profil, commentaire, formation_lvl)
			VALUES
			(:username, :password, :longName, :firstname, STR_TO_DATE(:birthday, '%Y-%m-%d'), :mail, :city, 
			:postCode, :adresse, :tel, :profil, :commentaire, :formation_lvl) ";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("username", $user->username);
		$stmt->bindValue("password", $user->password);
		$stmt->bindValue("longName", $user->long_name);
		$stmt->bindValue("firstname", $user->firstname);
		$stmt->bindValue("birthday", $user->birthday);
		$stmt->bindValue("mail", $user->mail);
		$stmt->bindValue("city", $user->city);
		$stmt->bindValue("postCode", $user->post_code);
		$stmt->bindValue("adresse", $user->adresse);
		$stmt->bindValue("tel", $user->tel);
		$stmt->bindValue("profil", $user->profil);
		$stmt->bindValue("commentaire", $user->commentaire);
		$stmt->bindValue("formation_lvl", $user->formation_lvl);
		$stmt->execute();

		return $this->db->lastInsertId();
	}

	public function updateUser($user) {
		$sql = "
			UPDATE user
			SET username = :username,
			long_name = :longName,
			firstname = :firstname,
			birthday = STR_TO_DATE(:birthday, '%Y-%m-%d'),
			mail = :mail,
			city = :city,
			post_code = :postCode,
			adresse = :adresse,
			tel = :tel,
			profil = :profil,
			commentaire = :commentaire,
			formation_lvl = :formation_lvl
			WHERE id = :id
			";
			
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id", $user->id);
		$stmt->bindValue("username", $user->username);
		$stmt->bindValue("longName", $user->long_name);
		$stmt->bindValue("firstname", $user->firstname);
		$stmt->bindValue("birthday", $user->birthday);
		$stmt->bindValue("mail", $user->mail);
		$stmt->bindValue("city", $user->city);
		$stmt->bindValue("postCode", $user->post_code);
		$stmt->bindValue("adresse", $user->adresse);
		$stmt->bindValue("tel", $user->tel);
		$stmt->bindValue("profil", $user->profil);
		$stmt->bindValue("commentaire", $user->commentaire);
		$stmt->bindValue("formation_lvl", $user->formation_lvl);
		$stmt->execute();
	}

	public function addRight($user, $unites) {
		$sql = "DELETE FROM has_access WHERE user_id = :user";
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("user", $user);
		$stmt->execute();

		$sql = "INSERT INTO has_access
				VALUES 
				(:user, :unite)	";
		foreach($unites as $unite) {
			$stmt = $this->db->prepare($sql);
			$stmt->bindValue("user", $user);
			$stmt->bindValue("unite", $unite);
			$stmt->execute();
		}
	}
}
?>
