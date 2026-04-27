<?php //on utilise ici une autre stratégie que celle du routeur que vous avez vu en PPE1 pour uniformiser les pages de l'application. Il s'agit d'inclure sur chaque page du site des entetes et bas de page identiques 
include "header.php";

?>
<div class="container">
	<h1>Affichage de la fiche</h1>

	<?php
	$estValide = true;
	$association = 0;
	$image = "";
	$pseudo = "pas de pseudo transmis";
	$nom = "pas de nom transmis";
	$prenom = "pas de prénom transmis";
	$statut = "";
	$dateNaissance = '2000-01-01';
	$avatar = 1;
	$adresse = "";
	$pays = "";

	if (isset($_GET['choix']) && $_GET['choix'] != "") {
		$asso = htmlentities($_GET['choix']);
		$requetePrepare = $connexion->prepare('SELECT libelleAssociation, imageAssociation, descriptionAssociation FROM association WHERE idAssociation = :idAssociation ');
		$requetePrepare->bindParam(':idAssociation', $asso, PDO::PARAM_INT);
		$resultats = $requetePrepare->execute();
		$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ); // on dit qu'on veut que le résultat soit récupérable sous forme d'objet
		$description = $ligne->descriptionAssociation;
		$image = "images/associations/" . $ligne->imageAssociation;
		$libelleAsso = $ligne->libelleAssociation;
	}

	if (isset($_POST['pseudo']) && $_POST['pseudo'] != "") {
		$pseudo = htmlspecialchars($_POST['pseudo']);
		// Vérification de la contrainte de pseudo unique
		$requetePrepare = $connexion->prepare('SELECT pseudo FROM utilisateur WHERE pseudo = :pseudo ');
		$requetePrepare->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
		$resultats = $requetePrepare->execute();
		$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ);
		if ($ligne) {
			$estValide = false;
		}
	}
	if (isset($_POST['nom']) && $_POST['nom'] != "") {
		$nom = htmlspecialchars($_POST['nom']);
		// Vérification de la contrainte au moins de 1 caractères, que des lettres (majuscules et minuscules) ou caractères spéciaux (espace, apostrophe, tiret)
		if (!preg_match("/^[a-zA-Z\s\'\-]+$/", $nom)) {
			$estValide = false;
		}
	}
	if (isset($_POST['prenom']) && $_POST['prenom'] != "") {
		$prenom = htmlspecialchars($_POST['prenom']);
		// Vérification de la contrainte au moins de 1 caractères, que des lettres (majuscules et minuscules) ou caractères spéciaux (espace, apostrophe, tiret)
		if (!preg_match("/^[a-zA-Z\s\'\-]+$/", $prenom)) {
			$estValide = false;
		}
	}
	if (isset($_POST['statut']) && $_POST['statut'] != "") {
		$statut = $_POST['statut'];
	}
	if (isset($_POST['dateNaissance']) && $_POST['dateNaissance'] != "") {
		$dateNaissance = htmlspecialchars($_POST['dateNaissance']);
		// Vérification de la contrainte sur un format de date et doit correspondre à un âge > 15 ans
		$age = date_diff(date_create($dateNaissance), date_create('now'))->y;
		if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $dateNaissance) || $age < 15) {
			$estValide = false;
		}
	}
	if (isset($_POST['avatar']) && $_POST['avatar'] != "") {
		$avatar = htmlspecialchars($_POST['avatar']);
	}

	if (isset($_POST['civilite']) && $_POST['civilite'] != "") {
		$civilite = htmlspecialchars($_POST['civilite']);
	}
	if (isset($_POST['email']) && $_POST['email'] != "") {
		$email = htmlspecialchars($_POST['email']);
		// Vérification de la contrainte sur un format d’adresse mail
		if (!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]{2,}\.[a-zA-Z]{2,4}$/", $email)) {
			$estValide = false;
		}
	}

	if (isset($_POST['adresse']) && $_POST['adresse'] != "") {
		$adresse = htmlspecialchars($_POST['adresse']);
	}
	if (isset($_POST['motDePasse']) && $_POST['motDePasse'] != "") {
		$motDePasse = htmlspecialchars($_POST['motDePasse']);
		// Vérification de la contrainte Au moins 8 caractères, au moins 1 chiffre, au moins une majuscule et une minuscule.
		if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", $motDePasse)) {
			$estValide = false;
		}
	}
	if (isset($_POST['confirmationMotDePasse']) && $_POST['confirmationMotDePasse'] != "") {
		$confirmationMotDePasse = htmlspecialchars($_POST['confirmationMotDePasse']);
	}

	if (isset($_POST['pays']) && $_POST['pays'] != "") {
		$pays = htmlspecialchars($_POST['pays']);
	}
	if (isset($_POST['newsletter']) && $_POST['newsletter'] != "") {
		if ($_POST['newsletter'] == "on"){
			$newsletter = 1;
		}
	} else {
		$newsletter = 0;
	}

	$resultat = false;
	if ($estValide) {
		$requete = "INSERT INTO utilisateur (pseudo, nom, prenom, idAssociation, idStatut, civilite, adresseMail, dateNaissance, adresse, motPasse, id_GalerieAvatar, id_pays, newsletter) VALUES (:pseudo, :nom, :prenom, :association, :statut, :civilite, :email, :dateNaissance, :adresse, :motDePasse, :avatar, :pays, :newsletter)";
		$requetePrepare = $connexion->prepare($requete);
		$requetePrepare->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
		$requetePrepare->bindParam(':nom', $nom, PDO::PARAM_STR);
		$requetePrepare->bindParam(':prenom', $prenom, PDO::PARAM_STR);
		$requetePrepare->bindParam(':association', $asso, PDO::PARAM_INT);
		$requetePrepare->bindParam(':statut', $statut, PDO::PARAM_INT);
		$requetePrepare->bindParam(':civilite', $civilite, PDO::PARAM_INT);
		$requetePrepare->bindParam(':email', $email, PDO::PARAM_STR);
		$requetePrepare->bindParam(':dateNaissance', $dateNaissance, PDO::PARAM_STR);
		$requetePrepare->bindParam(':adresse', $adresse, PDO::PARAM_STR);
		$motDePasseHash = password_hash($motDePasse, PASSWORD_DEFAULT);
		$requetePrepare->bindParam(':motDePasse', $motDePasseHash, PDO::PARAM_STR);
		$requetePrepare->bindParam(':motDePasse', $motDePasse, PDO::PARAM_STR);
		$requetePrepare->bindParam(':avatar', $avatar, PDO::PARAM_INT);
		$requetePrepare->bindParam(':pays', $pays, PDO::PARAM_INT);
		$requetePrepare->bindParam(':newsletter', $newsletter, PDO::PARAM_INT);
		$resultat = $requetePrepare->execute();
	}


	if ($resultat) {
	?>
		<div class="alert alert-success">Fiche bien enregistrée pour l'association <?php echo $libelleAsso; ?></div>
		<div class="row">
			<div class="col-md-3">
				<img class="img-responsive" src="<?php echo $image; ?>" width="150px" alt="logo"/>
			</div>

			<?php
			$requetePrepare = $connexion->prepare('SELECT lienImage FROM galerieavatar WHERE id = :avatar ');
			$requetePrepare->bindParam(':avatar', $avatar, PDO::PARAM_INT);			
			$resultats = $requetePrepare->execute();
			$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ);
			$lienImageAvatar = $ligne->lienImage;
			?>
			<div class="col-md-3">
				<u>Avatar :</u> <img src="images/<?php echo $lienImageAvatar; ?>" alt="avatar" />
			</div>

			<div class="col-md-3">
				<u>Pseudo :</u> <?php echo $pseudo; ?>
			</div>

			<div class="col-md-3">
				<u>Nom :</u> <?php echo $nom; ?>
			</div>

			<div class="col-md-3">
				<u>Prénom : </u> <?php echo $prenom; ?>
			</div>

			<div class="col-md-3">
				<u>Date de naissance :</u> <?php echo $dateNaissance; ?>
			</div>

			<?php
			$requetePrepare = $connexion->prepare('SELECT libelleStatut FROM statut WHERE idStatut = :statut AND idAssociation = :association ');
			$requetePrepare->bindParam(':statut', $statut, PDO::PARAM_INT);
			$requetePrepare->bindParam(':association', $asso, PDO::PARAM_INT);
			$resultats = $requetePrepare->execute();
			$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ);
			?>
			<div class="col-md-3">
				<u>Statut </u> <?php echo $ligne->libelleStatut; ?>
			</div>

			<?php
			$requetePrepare = $connexion->prepare('SELECT libelle FROM civilite WHERE id = :civilite ');
			$requetePrepare->bindParam(':civilite', $civilite, PDO::PARAM_INT);
			$resultats = $requetePrepare->execute();
			$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ);
			$civilite = $ligne->libelle;
			?>
			<div class="col-md-3">
				<u>Civilité :</u> <?php echo $civilite; ?>
			</div>

			<div class="col-md-3">
				<u>Email :</u> <?php echo $email; ?>
			</div>

			<div class="col-md-3">
				<u>Adresse :</u> <?php echo $adresse; ?>
			</div>

			<div class="col-md-3">
				<u>Mot de passe :</u> <?php echo $motDePasse; ?>
			</div>

			<?php
			$requetePrepare = $connexion->prepare('SELECT libelle FROM pays WHERE id = :pays ');
			$requetePrepare->bindParam(':pays', $pays, PDO::PARAM_INT);
			$resultats = $requetePrepare->execute();
			$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ);
			$pays = $ligne->libelle;
			?>
			<div class="col-md-3">
				<u>Pays :</u> <?php echo $pays; ?>
			</div>

			<?php 
			if ($newsletter == 1){
				$newsletter = "oui";
			} else {
				$newsletter = "non";
			}
			?>
			<div class="col-md-3">
				<u>Newsletter :</u> <?php echo $newsletter; ?>
			</div>

		</div>
	<?php
	} else {
	?>
		<div class="row alert alert-danger">
			<strong>Erreur</strong> La fiche n'a pas pu être enregistrée
		</div>
	<?php
	}
	?>
</div>
<?php
include "footer.php";
?>