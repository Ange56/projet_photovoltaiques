<?php 
?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <link href="../front/css/connexion.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Lalezar&family=Marko+One&family=Roboto&family=Stint+Ultra+Expanded&display=swap" rel="stylesheet"><!--importation de Roboto et Lalezar-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"><!--importation des icones de fontawesome-->

        <!--<script src="script.js"></script>-->
        <title> Connexion </title>
    </head>
    <body>
        
        <header>
            <nav class="navbar-custom d-flex justify-content-between align-items-center px-4">
			  <!-- Gauche : logo + titre -->
			    <div class="d-flex align-items-center">
			        
                    <a href="../front/html/accueil.html">
                        <img src="../images/logo.png" alt="Logo" class="logo-img me-2">
                    </a>
                    <span class="navbar-title">Panoneau</span>
			    </div>

                
			  <!-- Centre : boutons -->
			    <div class="d-flex gap-2">
                    <a href="../front/html/accueil.html" class="nav-button">Accueil</a>
                    <a href="../front/html/recherche.html" class="nav-button">Recherche</a>
                    <a href="../front/html/carte.html" class="nav-button">Carte</a>
                    <a href="connexion.php" class="nav-button active">Connexion</a>
			    </div>
			</nav>
        </header>

        <main class="main-content">
            <div class="light_blue container">
                <h1 class="text-center">Connectez-vous</h1>

                <?php if (!empty($messageErreur)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($messageErreur) ?>
                </div>


                <?php endif; ?>

                <form class="formulaire d-flex flex-column align-items-center" action="../php/connexion_medecin.php" method="POST">
                        
                        <div class="mb-3 w-100">
                            <label for="mail1" class="form-label">Saisissez votre adresse mail : *</label>
                            <div class="form-floating">
                                <input type="email" class="form-control" id="mail" name="mail" placeholder="nom@mail.com" required>
                                <label for="mail">nom@mail.com</label>
                            </div>
                        </div>
                        
                        <div class="mb-3 w-100">
                            <label for="motdepasse" class="form-label">Saisissez votre mot de passe : *</label>
                            <div class="form-text">
                                Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule et un chiffre.
                            </div>
                            <div class="form-floating">
                                <input type="password" class="form-control" id="motdepasse" name="motdepasse" placeholder="**********"  pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,}$"  required>
                                <label for="motdepasse">**********</label>
                            </div>
                            
                            <div class="mdp_oublie">
                                <a href="../html/mot_passe.html">Mot de passe oublié ?</a>
                            </div>
                        </div>
                    
                    
                    <div class="d-flex justify-content-center">
                        <input class="btn dark_blue" type="submit" value="Se connecter">
                    </div>
                </form>
            </div>
        </main>


         <!-- Footer -->
        <footer class="footer-custom">
            <div class="footer-content">
                <!-- Gauche : Noms avec LinkedIn -->
                <div class="footer-left">
                    <span class="footer-name">Tallulah DRENO-TABOT 
                        <a class="linkedin-link" target="_blank" href="https://www.linkedin.com/in/tallulah-dreno-tabot-9406842a3/">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </span>
                    <span class="footer-name">Angèle STUTZ 
                        <a class="linkedin-link" target="_blank" href="https://www.linkedin.com/in/ang%C3%A8le-stutz-2b76832a3/">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </span>
                    <span class="footer-name">Etienne DECAMPS 
                        <a class="linkedin-link" target="_blank" href="#">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    </span>
                </div>

                <!-- Centre : Groupe -->
                <div class="footer-center">
                    <span class="footer-group">Groupe 11</span>
                </div>

                <!-- Droite : Année -->
                <div class="footer-right">
                    <span class="footer-year">2025</span>
                </div>
            </div>
        </footer>

    </body>
</html>