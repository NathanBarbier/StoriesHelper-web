<?php 
require_once "layouts/entete.php";
?>

<div class="col-10">

    <div class="row mt-4">
        <div class="col pt-4" style="height:35vh">
            <a href="<?= CONTROLLERS_URL ?>admin/creationProjets.php" class="aVignette">
                <div class="bg-info mx-auto rounded vignette" style="height: 85%;width: 85%; box-shadow:grey">
                    <div class="row">
                        <div class="col">
                            <div style="height: 10vh; width:10vh; margin-top:9vh; margin-left:2vh">
                                <img src="<?= IMG_URL ?>folder.png" class="img-fluid">
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mt-4">Création des projets</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col pt-4" style="height:35vh">
            <a href="<?= CONTROLLERS_URL ?>admin/listeProjets.php" class="aVignette">
                <div class=" bg-info mx-auto rounded vignette1" style="height: 85%;width: 85%;">
                    <div class="row">
                        <div class="col">
                            <div style="height: 10vh; width:10vh; margin-top:9vh; margin-left:2vh">
                                <img src="<?= IMG_URL ?>list.png" width="120%">
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mt-4">Liste des projets</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col pt-4" style="height:35vh">
            <a href="#" class="aVignette">
                <div class="bg-info mx-auto rounded vignette2" style="height: 85%;width: 85%">
                    <div class="row">
                        <div class="col">
                            <div style="height: 10vh; width:10vh; margin-top:9vh; margin-left:2vh">
                                <img src="#" class="img-fluid">
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mt-4"></h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col pt-4" style="height:35vh">
            <div class=" bg-info mx-auto rounded vignette3" style="height: 85%;width: 85%">
            
            </div>
        </div>
    </div>

<?php
require_once "layouts/pied.php";
?>