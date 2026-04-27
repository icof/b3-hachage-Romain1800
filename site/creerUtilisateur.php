<!-- script permettant de vérifier que les deux mots de passe saisis sont identiques -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var mdp = document.getElementById("motDePasse");
		var confirm_mdp = document.getElementById("confirmationMotDePasse");
		confirm_mdp.addEventListener("input", function(event) {
			// confirm_mdp.setCustomValidity("la confirmation du mdp vaut : " + confirm_mdp.value + " et le mdp vaut : " + mdp.value + " !");
			if (mdp.value != confirm_mdp.value) {
				confirm_mdp.setCustomValidity("Les mots de passes doivent être identiques!");
			} else {
				confirm_mdp.setCustomValidity("");
			}
		});
	});
</script>

<!-- script permettant de vérifier que l'utilisateur a plus de 15 ans -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var dateNaissanceElem = document.getElementById("dateNaissance");
		dateNaissanceElem.addEventListener("input", function(event) {
			var dateNaissance = new Date(dateNaissanceElem.value);
			var dateActuelle = new Date();
			var age = dateActuelle.getFullYear() - dateNaissance.getFullYear();
			if (age < 15) {
				dateNaissanceElem.setCustomValidity("Vous devez avoir plus de 15 ans!");
			} else {
				dateNaissanceElem.setCustomValidity("");
			}
		});
	});
</script>

<!-- script AJAX permettant de vérifier que le pseudo n'existe pas déjà -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var pseudoElem = document.getElementById('pseudo');
		pseudoElem.addEventListener('input', function() {
			var pseudo = this.value;
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'traitementsAsynchrone.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				if (this.responseText == "existe") {
					pseudoElem.setCustomValidity("Ce pseudo existe déjà !");
				} else {
					pseudoElem.setCustomValidity("");
				}
			};
			xhr.send('action=verifpseudo&pseudo=' + pseudo);
		});
	});
</script>

<!-- script AJAX permettant de vérifier que l'adresse mail n'existe pas déjà et produire une alerte js si c'est la cas -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var emailElem = document.getElementById('email');
		emailElem.addEventListener('input', function() {
			var email = this.value;
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'traitementsAsynchrone.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				if (this.responseText == "existe") {
					alert("Cette adresse mail existe déjà !");
				}
			};
			xhr.send('action=verifemail&email=' + email);
		});
	});
</script>

<!-- script permettant de charger les avatars en fonction de l'âge. Recoit en réponse un tableau JSON contenant les avatars correspondants à l'âge. Affiche les avatars dans le div avatar
 -->
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var dateNaissanceElem = document.getElementById("dateNaissance");
		dateNaissanceElem.addEventListener("input", function(event) {
			var dateNaissance = new Date(dateNaissanceElem.value);
			var dateActuelle = new Date();
			var age = dateActuelle.getFullYear() - dateNaissance.getFullYear();
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'traitementsAsynchrone.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				var avatars = JSON.parse(this.responseText);
				var avatarElem = document.getElementById('avatar-grid');
				avatarElem.innerHTML = '';
				for (var i = 0; i < avatars.length; i++) {
					var id = avatars[i].id;
					var lienImage = avatars[i].lienImage;
					var input = '<div class="avatar-item"><input type="radio" id="avatar' + id + '" name="avatar" value="' + id + '">';
					var label = '<label for="avatar' + id + '"><img src="images/' + lienImage + '" width="75px" /></label></div>';
					avatarElem.innerHTML += input + label;
				}
			};
			xhr.send('action=chargeavatars&age=' + age);
		});
	});
</script>


<?php //on utilise ici une autre stratégie que celle du routeur que vous avez vu en PPE1 pour uniformiser les pages de l'application. Il s'agit d'inclure sur chaque page du site des entetes et bas de page identiques 
include "header.php";

?>

<div class="container">

	<?php if (isset($_GET['choix'])) {
		$asso = htmlentities($_GET['choix']);
	?>

		<?php
		$SQL1 = "SELECT imageAssociation, descriptionAssociation FROM association WHERE idAssociation = " . $asso;
		$requetePrepare = $connexion->prepare('SELECT imageAssociation, descriptionAssociation FROM association WHERE idAssociation = :asso');
		$requetePrepare->bindParam(':asso', $asso, PDO::PARAM_INT);
		$resultats = $requetePrepare->execute();
		$ligne = $requetePrepare->fetch(PDO::FETCH_OBJ); // on dit qu'on veut que le résultat soit récupérable sous forme d'objet
		$description = $ligne->descriptionAssociation;
		$image = "images/associations/" . $ligne->imageAssociation;
		?>

		<h2><?php echo $description; ?></h2>

		<form method="post" id="saisie" action="confirmerCreationUtilisateur.php?choix=<?php echo $asso; ?>">
			<div class="row">
				<div class="col-md-3">
					<img class="img-responsive" src="<?php echo $image; ?>" alt="logo de l'association" width="150px" />
				</div>

				<!-- pseudo : champ de type texte, obligatoire, compris entre 4 et 15 caractères -->
				<div class="col-md-3">
					<label for="nom">Quel est votre pseudo ?</label>
					<input rows="1" class="form-control form-control-lg" id="pseudo" name="pseudo" required pattern=".{4,15}"></input>
					<small class="form-text text-muted">Le pseudo doit comporter entre 4 et 15 caractères.</small>
				</div>

				<!-- nom : champ de type texte, obligatoire, Au moins de 1 caractères, que des lettres (majuscules et minuscules) ou caractères spéciaux (espace, apostrophe, tiret) -->
				<div class="col-md-3">
					<label for="nom">Quel est votre nom ?</label>
					<input type="text" class="form-control form-control-lg" id="nom" name="nom" required pattern="^[a-zA-Z\s'\-]+$"></input>
					<small class="form-text text-muted">Le nom doit comporter au moins 1 caractère et ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.</small>
				</div>

				<!-- prenom : champ de type texte, obligatoire, Au moins de 1 caractères, que des lettres (majuscules et minuscules) ou caractères spéciaux (espace, apostrophe, tiret) -->
				<div class="col-md-3">
					<label for="prenom">Quel est votre prénom ?</label>
					<input rows="1" class="form-control form-control-lg" id="prenom" name="prenom" required pattern="^[a-zA-Z\s'\-]+$"></input>
					<small class="form-text text-muted">Le nom doit comporter au moins 1 caractère et ne peut contenir que des lettres, des espaces, des apostrophes et des tirets.</small>
				</div>

				<!-- statut : champ de type select alimenté depuis table statut : rien à modifier -->
				<div class="col-md-3">
					<label for="camp">Quel est votre statut ?</label>
					<select name="statut" id="statut" class="form-control form-control-lg">
						<?php
						// Requête pour obtenir les statuts
						$requetePrepare = $connexion->prepare('SELECT idStatut, libelleStatut FROM association A JOIN statut S ON A.idAssociation = S.idAssociation WHERE S.idAssociation = :asso');
						$requetePrepare->bindParam(':asso', $asso, PDO::PARAM_INT);
						$requetePrepare->execute();
						$statuts = $requetePrepare->fetchAll(PDO::FETCH_OBJ);
						// Parcourir les résultats et créer une option pour chaque statut
						foreach ($statuts as $ligne) {
							echo '<option value="' . $ligne->idStatut . '">' . $ligne->libelleStatut . '</option>';
						}
						?>
					</select>
				</div>

				<!-- civilité : bouton radio alimenté depuis table civilité, valeur "non renseigné" cochée par défaut -->
				<div class="col-md-3">
					<label for="civilite">Civilité</label>
					<?php
					// Requête pour obtenir les civilités
					$requetePrepare = $connexion->prepare('SELECT id, libelle FROM civilite');
					$requetePrepare->execute();
					$civilites = $requetePrepare->fetchAll(PDO::FETCH_OBJ);

					// Parcourir les résultats et créer un bouton radio pour chaque civilité
					foreach ($civilites as $ligne) {
						$id = $ligne->id;
						$libelle = $ligne->libelle;

						$checked = $libelle == 'non renseigné' ? 'checked' : '';

						echo '<input type="radio" id="civilite' . $id . '" name="civilite" value="' . $id . '" ' . $checked . '>';
						echo '<label for="civilite' . $id . '">' . $libelle . '</label>';
					}
					?>
				</div>

				<!-- date de naissance : champ de type date, obligatoire -->
				<div class="col-md-3">
					<label for="dateNaissance">Date de naissance</label>
					<input type="date" class="form-control" id="dateNaissance" name="dateNaissance" required>
				</div>

			</div>
			<div class="row">
				<!-- avatar : Les avatars proposés dépendent de l’âge et de la civilité saisis (contrôle à coder en AJAX) -->
				<div class="col-md-12">
					<label for="avatar">Avatar</label>
					<div id="avatar-grid" class="avatar-grid">
						<?php
						// Requête pour obtenir les avatars
						$requetePrepare = $connexion->prepare('SELECT id, lienImage FROM galerieavatar ');
						$requetePrepare->execute();
						$avatars = $requetePrepare->fetchAll(PDO::FETCH_OBJ);

						// Parcourir les résultats et créer un bouton radio pour chaque image
						foreach ($avatars as $ligne) {
							$id = $ligne->id;
							$lienImage = $ligne->lienImage;
							echo '<div class="avatar-item">';
							echo '<input type="radio" id="avatar' . $id . '" name="avatar" value="' . $id . '">';
							echo '<label for="avatar' . $id . '"><img src="images/' . $lienImage . '" width="75px" /></label>';
							echo '</div>';
						}
						?>
					</div>
				</div>
			</div>

			<div class="row">

				<!-- adresse mail : champ de type email, obligatoire -->
				<div class="col-md-4">
					<label for="email">Adresse mail</label>
					<input type="email" class="form-control" id="email" name="email" required>
				</div>

				<!-- adresse : champ de type textarea -->
				<div class="col-md-4">
					<label for="adresse">Adresse</label>
					<textarea class="form-control" id="adresse" name="adresse" rows="3"></textarea>
				</div>

				<!-- pays : champ de type select alimenté depuis table pays -->
				<div class="col-md-4">
					<label for="pays">Pays</label>
					<select name="pays" id="pays" class="form-control form-control-lg">
						<?php
						// Requête pour obtenir les pays
						$requetePrepare = $connexion->prepare('SELECT id, libelle FROM pays');
						$requetePrepare->execute();
						$pays = $requetePrepare->fetchAll(PDO::FETCH_OBJ);
						// Parcourir les résultats et créer une option pour chaque pays
						foreach ($pays as $ligne) {
							echo '<option value="' . $ligne->id . '">' . $ligne->libelle . '</option>';
						}
						?>
					</select>
				</div>

			</div>

			<div class="row">

	<!-- mot de passe -->
	<div class="col-md-4">
		<label for="motDePasse">Mot de passe</label>
		<input type="password" class="form-control" id="motDePasse" name="motDePasse" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
		<p id="forceMotDePasse"></p>
		<small class="form-text text-muted">
			Le mot de passe doit comporter au moins 8 caractères, dont au moins 1 chiffre, 1 majuscule et 1 minuscule.
		</small>
	</div>

	<!-- confirmation -->
	<div class="col-md-4">
		<label for="confirmationMotDePasse">Confirmation du mot de passe</label>
		<input type="password" class="form-control" id="confirmationMotDePasse" name="confirmationMotDePasse" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
	</div>

</div>

			<div class="row">
				<!-- inscription newsletter : champ de type checkbox, selectionné par défaut -->
				<div class="col-md-4">
					<input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" checked>
					<label class="form-check-label" for="newsletter">Inscription newsletter</label>
				</div>
			</div>


			<div class="row">
				<button type="submit" class="btn btn-primary btn-block">Enregistrer et afficher la fiche utilisateur</button>
			</div>
		</form>



	<?php } else {
?>
    <div class="alert alert-danger">Vous devez faire un choix</div>
<?php } ?>
</div>

<script>
window.onload = function () {
    var mdp = document.getElementById("motDePasse");
    var forceMdp = document.getElementById("forceMotDePasse");

    if (!mdp || !forceMdp) return;

    mdp.addEventListener("input", function () {
        var motDePasse = mdp.value;
        var force = 0;

        if (motDePasse.length >= 8) force++;
        if (/[a-z]/.test(motDePasse)) force++;
        if (/[A-Z]/.test(motDePasse)) force++;
        if (/[0-9]/.test(motDePasse)) force++;
        if (/[^A-Za-z0-9]/.test(motDePasse)) force++;

        if (force <= 2) {
            forceMdp.textContent = "Force : faible";
        } else if (force <= 4) {
            forceMdp.textContent = "Force : moyenne";
        } else {
            forceMdp.textContent = "Force : forte";
        }
    });
};
</script>

<?php
include "footer.php";
?>