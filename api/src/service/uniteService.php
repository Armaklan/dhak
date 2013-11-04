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

class UniteService {

	private $db;
	private $session;
	private $log;

	public function __construct($db, $session, $log)
    {
        $this->db = $db;
        $this->session = $session;
        $this->log = $log;
	}

	public function getInformation($idUnite) {
		$this->log->info("Debut recherche information " . $idUnite);
		$sql = "SELECT 
					unite.id as id,
					branche.name as branche_name, 
					groupe.name as groupe_name, 
					district.name as district_name,
					province.name as province_name,
					unite.size as size,
					unite.nb_sizaine as nb_sizaine,
					unite.commentaire as commentaire
				FROM unite 
				JOIN branche
				ON unite.branche_id = branche.id
				JOIN groupe
				ON unite.groupe_id = groupe.id
				JOIN district 
				ON groupe.district_id = district.id
				JOIN province
				ON district.province_id = province.id
				WHERE unite.id = ?";
	    return $this->db->fetchAssoc($sql, array($idUnite));
	}

	public function getMaitrise($idUnite) {
		$this->log->info("Récupération de la maîtrise " . $idUnite);
		$sql = "SELECT
					user.id, user.username, user.password, user.long_name, user.firstname, 
					DATE_FORMAT(user.birthday, '%d/%m/%Y') as birthday, user.mail, user.city, user.post_code, 
					user.adresse, user.tel, user.profil, user.commentaire, 
					user.formation_lvl, formation.shortname as formation_name
				FROM user
				JOIN asso_unite_user asso
				ON user.id = asso.user_id
				LEFT JOIN formation
				ON user.formation_lvl = formation.lvl
				WHERE
				asso.unite_id = ?";
		return $this->db->fetchAll($sql, array($idUnite));
	}

	public function addUserInMaitrise($idUnite, $idUser) {
		$sql = "INSERT INTO asso_unite_user (user_id, unite_id) 
				VALUES (:user, :unite)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("user", $idUser);
		$stmt->bindValue("unite", $idUnite);
		$stmt->execute();
	}

	public function deleteUserInMaitrise($idUnite, $idUser) {
		$sql = "DELETE FROM asso_unite_user 
				WHERE user_id = :user
				AND unite_id = :unite";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("user", $idUser);
		$stmt->bindValue("unite", $idUnite);
		$stmt->execute();
	}

	public function updateUnite($unite) {
		$sql = "UPDATE unite
				SET size = :size,
				nb_sizaine = :nb_sizaine,
				commentaire = :commentaire
				WHERE
				id = :unite";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("size", $unite->size);
		$stmt->bindValue("nb_sizaine", $unite->nb_sizaine);
		$stmt->bindValue("commentaire", $unite->commentaire);
		$stmt->bindValue("unite", $unite->id);
		$stmt->execute();
	}

}

?>
