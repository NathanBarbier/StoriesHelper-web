<?php
require_once "layouts/header.php";
?>

    <div class="row position-relative">
        <h3 class="text-center mx-auto w-25 mb-2 pb-1 bg-white underline sticker">Liste des projets</h3>
        <input id="search-bar" type="text" class="form-control w-25 me-4 position-absolute top-0 end-0" style="border-radius: 15px; opacity: 90%">
        <i id="search-minifier" class="bi bi-search position-absolute top-0 end-0 me-4"></i>
    </div>

    <!-- Delete project Modal -->
    <div class="modal" id="delete-project-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog delete-project-dialog position-absolute start-50 translate-middle" style="top:40%; width: 40vw; height: 40vh">
            <div class="modal-content" style="height: inherit;">
                <div class="modal-body position-relative pt-0 text-center">
                    <h4 class="mx-auto border-bottom w-75 mt-3 mb-3">Confirmation de suppression de projet.</h4>
                    <b class="mt-2">Êtes-vous sûr de vouloir supprimer le projet ?</b>
                    <br>
                    <b class="mt-1"><span style="color: red;">(Cette action est définitive et supprimera toute donnée étant en lien avec celui-ci)</span><b>
                    <div class="row mx-2 mt-5" style="font-size: large;">
                        <div class="col-6">
                            <a id="delete-project-btn-conf" class="pt-2 w-100 custom-button danger" href="<?= CONTROLLERS_URL ?>admin/projectList.php?action=deleteProject" style="height: 55px; padding: unset">
                                Supprimer
                            </a>
                        </div>
                        <div class="col-6">
                            <a type="button" id="cancel-delete-btn" class="pt-2 w-100 custom-button warning" style="height: 55px; padding: unset">
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sticker mx-3" style="height:85vh; overflow:auto">
    <?php
    if(count($Organization->getProjects()) > 0)
    { ?>
        <div class="row mx-auto px-2 mt-3 justify-content-between">
            <div class="col-3 sticker text-center"><h5 class="mt-3">Nom</h5></div>
            <div class="col-2 sticker text-center"><h5 class="mt-3">Type</h5></div>
            <div class="col-3 sticker text-center"><h5 class="mt-3">État</h5></div>
            <div class="col-3 sticker text-center"><h5 class="mt-3">Options</h5></div>
        </div>

        <div id="projects-container" class="pb-3">
            <?php
            foreach($Organization->getProjects() as $Project)
            { ?>
            <div class="row sticker mx-2 mt-4 pb-2 h-auto d-flex align-items-center">
                <div class="col-3 text-center mx-auto"><b><?= $Project->getName() ?></b></div>
                <div class="col-2 text-center mx-auto"><b><?= $Project->getType() ?></b></div>
                <div class="col-3 text-center mx-auto"><b><?= $Project->isActive() == 1 ? '<span style="color:green">Ouvert</span>' : '<span style="color:red">Archivé</span>' ?></b></div>
                <div class="col-3 text-center mx-auto">
                    
                    <input type="hidden" class="project-id" value="<?= $Project->getRowid() ?>">
                    
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <a href="<?= CONTROLLERS_URL ?>admin/projectDashboard.php?idProject=<?= $Project->getRowid() ?>" class="w-100 custom-button info btn-sm mt-1 px-1 pt-2 double-button-responsive">
                                Détails
                            </a>
                        </div>
                        <div class="col-12 col-lg-6">
                            <button class="w-100 del-project-btn custom-button danger btn-sm mt-1 px-1 double-button-responsive">
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
            } ?>

            <div id="load-more-line" class="radius text-center mx-auto mt-2 border hover" style="height: 5vh;width:33%;font-size: x-large">
                <a id="load-more" type="button" class="custom-link py-0" style="width: 100%; height: 100%">Load more</a>
            </div>
        </div>
        <?php
    } 
    else 
    { 
    ?>
        <div class="sticker w-75 mx-auto text-center mt-4">
            <h3 class="mt-2">Votre organisation n'a aucun projet.</h3>
        </div>
    <?php
    } 
    ?>
    </div>

    <script type="text/Javascript" src="<?= JS_URL ?>admin/projectList.min.js" defer></script>
<?php
require_once "layouts/footer.php";
?>