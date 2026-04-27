<?php
$_ENV['MYSQL_HOST'] = 'db'; // le chemin vers le serveur ("db" en version docker, "localhost" en version serveur local)
$_ENV['MYSQL_PORT'] = '3306'; // le port de connexion à la base de données
$_ENV['MYSQL_DATABASE'] = 'm2l_appli'; // le nom de votre base de données
$_ENV['MYSQL_USER'] = 'user-mdl'; // nom d'utilisateur pour se connecter
$_ENV['MYSQL_PASSWORD'] = 'mdp-mdl'; // mot de passe de l'utilisateur pour se connecter
