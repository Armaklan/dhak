<?php
/**
 * User entity managment
 *
 * @package campService
 * @author ZUBER Lionel <lionel.zuber@armaklan.org>
 * @version 0.1
 * @copyright (C) 2013 ZUBER Lionel <lionel.zuber@armaklan.org>
 * @license BSD
 */

class CampService {

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
		$this->log->info("Debut recherche information camp " . $idUnite);
		$sql = "SELECT 
					camp.id as id,
					camp.unite_id as unite_id,
					camp.duree as duree,
					camp.distance as distance,
					DATE_FORMAT(camp.debut, '%d/%m/%Y') as debut,
					camp.lieu as lieu
				FROM camp 
				WHERE camp.unite_id = ?";
	    $result = $this->db->fetchAssoc($sql, array($idUnite));

	    if( !($result['id'] > 0) ) {
	    	$this->log->info("Debut recherche information camp " . $idUnite);
	    	$sqlInsert = "INSERT INTO camp
	    		(unite_id)
	    		VALUES
	    		(:unite_id)";
	    	$stmt = $this->db->prepare($sqlInsert);
			$stmt->bindValue("unite_id", $idUnite);
			$stmt->execute();

	    	$result = $this->db->fetchAssoc($sql, array($idUnite));
	    }
	    return $result;
	}


	public function updateInformation($id, $detail) {
		$this->log->info("Update camp " . $id);
		$sql = "UPDATE camp
				SET duree = :duree,
				distance = :distance,
				lieu = :lieu,
				debut = STR_TO_DATE(:debut, '%d/%m/%Y')
	    		WHERE
	    		id = :id";
    	$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id", $id);
		$stmt->bindValue("duree", $detail->duree);
		$stmt->bindValue("distance", $detail->distance);
		$stmt->bindValue("lieu", $detail->lieu);
		$stmt->bindValue("debut", $detail->debut);
		$stmt->execute();
	}

	public function updateChefPresence($idUnite, $chefs) {
		$sql = "UPDATE asso_unite_user
			SET camp = :camp
			WHERE user_id = :user
			AND unite_id = :unite";

		foreach ($chefs as $chef) {
			$this->log->info("Update camp chef " . $chef->id . " unite : " . $idUnite . " camp " . $chef->camp);

			$stmt = $this->db->prepare($sql);
			if($chef->camp) {
				$stmt->bindValue("camp", 1);
			} else {
				$stmt->bindValue("camp", 0);
			}
			$stmt->bindValue("user", $chef->id);
			$stmt->bindValue("unite", $idUnite);
			$stmt->execute();
		}
	}

	public function getMaitrise($idUnite) {
		$this->log->info("Récupération de la maîtrise " . $idUnite);
		$sql = "SELECT
					user.id, user.username, user.password, user.long_name, user.firstname, 
					DATE_FORMAT(user.birthday, '%d/%m/%Y') as birthday, user.mail, user.city, user.post_code, 
					user.adresse, user.tel, user.profil, user.commentaire, 
					user.formation_lvl, formation.shortname as formation_name
				FROM user
				JOIN asso_camp_user asso
				ON user.id = asso.user_id
				LEFT JOIN formation
				ON user.formation_lvl = formation.lvl
				WHERE
				asso.unite_id = ?";
		return $this->db->fetchAll($sql, array($idUnite));
	}

	public function addUserInMaitrise($idUnite, $idUser) {
		$sql = "INSERT INTO asso_camp_user (user_id, unite_id) 
				VALUES (:user, :unite)";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("user", $idUser);
		$stmt->bindValue("unite", $idUnite);
		$stmt->execute();
	}

	public function deleteUserInMaitrise($idUnite, $idUser) {
		$sql = "DELETE FROM asso_camp_user 
				WHERE user_id = :user
				AND unite_id = :unite";

		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("user", $idUser);
		$stmt->bindValue("unite", $idUnite);
		$stmt->execute();
	}

}

?>
