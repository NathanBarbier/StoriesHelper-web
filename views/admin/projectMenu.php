<?php 
require_once "layouts/header.php";
?>
    <div class="row mt-4">
        <div class="col-xs-12 col-md-6 pt-4" style="height:35vh">
            <a href="<?= CONTROLLERS_URL ?>admin/projectCreation.php" class="aVignette">
                <div id="vignette0" class="bg-info mx-auto rounded vignette">
                    <div class="row">
                        <div class="col-4">
                            <div class="vignette-img-container">
                                <img src="<?= IMG_URL ?>folder.png" class="img-fluid">
                            </div>
                        </div>
                        <div class="col-8">
                            <h4 class="mt-5 text-start">Création des projets</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-md-6 pt-4" style="height:35vh">
            <a href="<?= CONTROLLERS_URL ?>admin/projectList.php" class="aVignette">
                <div id="vignette1" class="bg-info mx-auto rounded vignette">
                    <div class="row">
                        <div class="col-4">
                            <div class="vignette-img-container">
                                <img src="<?= IMG_URL ?>list.png" width="120%">
                            </div>
                        </div>
                        <div class="col-8">
                            <h4 class="mt-5 text-start">Liste des projets</h4>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
<?php
require_once "layouts/footer.php";
?>