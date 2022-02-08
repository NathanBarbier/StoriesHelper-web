<?php
require_once "layouts/entete.php";
?>

<div class="container">

    <?php if(!empty($errors)) { ?>
    <div class="position-relative mx-auto">
        <div class="alert alert-danger w-50 text-center position-absolute top-0 start-50 translate-middle-x">
        <?php foreach($errors as $error) { ?>
            <i class="bi bi-exclamation-triangle-fill"></i>
            <?php echo $error . "<br>";
        } ?>
        </div>
    </div>
    <?php } else if($message) { ?>
    <div class="position-relative mx-auto">
        <div class="alert alert-success w-50 text-center position-absolute top-0 start-50 translate-middle-x">
            <i class="bi bi-check-circle-fill"></i>
            <?= $message ?>
        </div>
    </div>
    <?php } ?>
    
    
    <div class="row">
        <form class="col-md-12 col-lg-6 mx-auto border-lg bg-white px-4 pb-3" method="post" action="<?= CONTROLLERS_URL ?>visiteur/connexion.php">
            <h1 class="text-center w-100 mx-auto border-bottom mt-2">Connexion</h1>
    
            <div class="form-floating mt-5">
                <input id="email" type="email" class="form-control" name="email" placeholder="Saisissez votre identifiant" value="<?= $email ?? '' ?>" maxlength="50" required>
                <label for="email" class="mb-1">Addresse email</label>
            </div>
    
            <div class="form-floating mt-3">
                <input id="password" type="password" class="form-control" name="password" placeholder="Saisissez votre mot de passe" required>
                <label for="password" class="mb-1">Mot de passe</label>
            </div>
            
            <div class="form-check mt-3">
                <input class="form-check-input" name="rememberMe" value="1" type="checkbox" id="rememberMe">
                <label class="form-check-label" for="rememberMe">Se souvenir de moi</label>
            </div>
            
            <div class="form-group text-center mt-3">
                <button type="submit" class="btn btn-outline-classic mt-3 w-100" name="envoi" value="1">Se connecter</button>
            </div>

            <div class="form-group text-center mt-2">
                <div class="mt-5">
                    Vous n'avez pas de compte ?
                </div>
                <a href="inscriptionOrganisation.php" class="btn btn-outline-classic w-md-100 w-lg-50 mt-3">Inscrivez-vous</a>
            </div>
        </form>
    </div>
</div>

<?php
require_once "layouts/pied.php";