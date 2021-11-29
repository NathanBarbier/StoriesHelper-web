<?php
//import all models
require_once "../../services/header.php";

$idOrganization = $_SESSION["idOrganization"] ?? false;
$rights = $_SESSION["rights"] ?? false;

if($rights === "admin")
{
    $tpl = "detailsProjet.php";

    $action = GETPOST('action');
    $idProject = GETPOST('idProject');
    $projectName = GETPOST('projectName');
    $description = GETPOST('description');
    $type = GETPOST('type');

    $teamName = GETPOST('teamName');
    $teamNameUpdate = GETPOST('teamNameUpdate');
    $teamId = GETPOST('teamId');
    $errors = GETPOST('errors');

    if($idProject)
    {
        $Project = new Project($idProject);

        $CurrentProject = new stdClass;

        $CurrentProject->rowid = $idProject;
        $CurrentProject->name = $Project->getName();
        $CurrentProject->description = $Project->getDescritpion();
        $CurrentProject->type = $Project->getType();
        $CurrentProject->active = intval($Project->getActive());

        $success = false;
        $refresh = false;

        if($errors)
        {
            $errors = unserialize($errors);
        }
        else
        {
            $errors = array();
        }

        $User = new User();
        $Team = new Team();
        $BelongsTo = new BelongsTo();

        $projectFreeUsers = $User->fetchFreeUsersByProjectId($idProject, $idOrganization);

        $projectFreeUsersIds = array();
        foreach($projectFreeUsers as $user)
        {
            $projectFreeUsersIds[] = $user->rowid;
        }

        $projectTeamsIds = array();
        
        $lines = $Team->fetchByProjectId($idProject);

        foreach($lines as $line)
        {
            $projectTeamsIds[] = $line->rowid;
        }

        $CurrentProject->teams = $Team->fetchByTeamIds($projectTeamsIds);

        foreach($CurrentProject->teams as $key => $team)
        {
            $CurrentProject->teams[$key]->members = $User->fetchByTeam($team->rowid);
        }

        $addingUsersIds = array();
        foreach($projectFreeUsers as $key => $user)
        {
            if(GETPOST('addingUser'.$key))
            {
                $addingUsersIds[] = GETPOST('addingUser'.$key);
            }
        }

        if($action == "archiveTeam")
        {
            if($teamId)
            {
                $status = $Team->updateActive(0, $teamId);

                if($status)
                {
                    $success = "Le tableau de l'équipe à bien été archivé.";
                    $refresh = true;
                }
                else
                {
                    $errors[] = "Une erreur innatendue est survenue.";
                }
            }
            else
            {
                $errors[] = "Vous n'avez pas sélectionné d'équipe.";
            }
        }

        if($action == "openTeam")
        {
            if($teamId)
            {
                $status = $Team->updateActive(1, $teamId);

                if($status)
                {
                    $success = "Le tableau de l'équipe à bien été ré-ouvert.";
                    $refresh = true;
                }
                else
                {
                    $errors[] = "Une erreur innatendue est survenue.";
                }
            }
            else
            {
                $errors[] = "Vous n'avez pas sélectionné d'équipe.";
            }
        }

        if($action == "openProject")
        {
            $status = $Project->updateActive(true, $idProject);

            if($status)
            {
                $success = "Le projet à bien été ré-ouvert.";
                $refresh = true;
            }
            else
            {
                $errors[] = "Une erreur innatendue est survenue.";
            }
        }

        if($action == "updateProject")
        {
            if($projectName && $description && $type)
            {
                $status = array();

                $status[] = $Project->updateName($projectName, $idProject);
                $status[] = $Project->updateDescription($description, $idProject);
                $status[] = $Project->updateType($type, $idProject);

                if(in_array(false, $status))
                {
                    $errors[] = "Une erreur inattendue est survenue.";
                }
                else
                {
                    $success = "Les informations du projet ont bien été mises à jour.";
                    $refresh = true;
                }
            }
            else
            {
                $errors[] = "Tous les champs ne sont pas remplis.";
            }
        }
        
        if($action == "addTeam")
        {
            if($teamName)
            {
                if($addingUsersIds)
                {
                    $status = array();
                    // create team with users
                    $status[] = $Team->create($teamName, $idOrganization, $idProject);

                    $idTeam = $Team->fetchMaxId()->rowid;

                    foreach($addingUsersIds as $idUser)
                    {
                        $status[] = $BelongsTo->create($idUser, $idTeam);
                    }

                    if(!in_array(false, $status))
                    {
                        $success = "L'équipe a bien été créée.";
                        $refresh = true;
                    }
                    else
                    {
                        $errors[] = "Une erreur inattendue est survenue.";
                    }
                }
                else
                {
                    // create team without users
                    $status = $Team->create($teamName, $idOrganization, $idProject);

                    if($status)
                    {
                        $success = "L'équipe a bien été créée.";
                        $refresh = true;
                    }
                    else
                    {
                        $errors[] = "Une erreur inattendue est survenue.";
                    }
                }
            }
            else
            {
                $errors[] = "L'équipe n'a pas de nom.";
            }
        }

        if($action == "deleteTeam")
        {
            if($teamId)
            {
                $status = $Team->delete($teamId);

                if($status)
                {
                    $success = "L'équipe a bien été supprimée.";
                    $refresh = true;
                }
                else
                {
                    $errors[] = "Une erreur innatendue est survenue.";
                }
            }
            else
            {
                $errors[] = "Vous n'avez pas sélectionné d'équipe";
            }
        }

        if($action == "updateTeam")
        {
            if($teamId)
            {
                $status = array();

                // changement de nom d'équipe
                if($teamNameUpdate)
                {
                    $status[] = $Team->updateName($teamNameUpdate, $teamId);
                }

                // ajout des users dans la team
                foreach($addingUsersIds as $idUser)
                {
                    $status[] = $BelongsTo->create($idUser, $teamId);
                }

                // suppression des users dans la team
                foreach($CurrentProject->teams as $team)
                {
                    if($team->rowid == $teamId)
                    {
                        foreach($team->members as $key => $member)
                        {
                            if(GETPOST('removingUser'.$key))
                            {
                                $fk_user = GETPOST('removingUser'.$key);
                                $status[] = $BelongsTo->delete($fk_user, $team->rowid);
                            }
                        }
                    }
                }

                // exit;

                if(!in_array(false, $status))
                {
                    $success = "L'équipe a bien été modifiée.";
                    $refresh = true;
                }
                else
                {
                    $errors[] = "Une erreur innatendue est survenue.";
                }
            }
            else
            {
                $errors[] = "Vous n'avez pas sélectionné d'équipe";
            }

        }

        if($action == "archive")
        {
            if($idProject)
            {    
                $status = $Project->updateActive(0, $idProject);

                if($status)
                {
                    $refresh = true;
                }
                else
                {
                    $errors[] = "Une erreur innatendue est survenue.";
                }
            }
            else
            {
                $errors[] = "Aucun projet n'a été sélectionné.";
            }
        }


        if($refresh)
        {
            $Project = new Project($idProject);

            $CurrentProject = new stdClass;

            $CurrentProject->rowid = $idProject;
            $CurrentProject->name = $Project->getName();
            $CurrentProject->description = $Project->getDescritpion();
            $CurrentProject->type = $Project->getType();
            $CurrentProject->active = $Project->getActive();

            $projectFreeUsers = $User->fetchFreeUsersByProjectId($idProject, $idOrganization);

            $projectFreeUsersIds = array();
            foreach($projectFreeUsers as $user)
            {
                $projectFreeUsersIds[] = $user->rowid;
            }
        
            $projectTeamsIds = array();
            
            $lines = $Team->fetchByProjectId($idProject);
            foreach($lines as $line)
            {
                $projectTeamsIds[] = $line->rowid;
            }
        
            $CurrentProject->teams = $Team->fetchByTeamIds($projectTeamsIds);

            foreach($CurrentProject->teams as $key => $team)
            {
                $CurrentProject->teams[$key]->members = $User->fetchByTeam($team->rowid);
            }
        }
    }
    else
    {
        $errors[] = "Aucun projet n'a été sélectionné.";
    }

    ?><script>
        const AJAX_URL = <?php echo json_encode(AJAX_URL); ?>;
        const CONTROLLERS_URL = <?php echo json_encode(CONTROLLERS_URL); ?>;
        const projectId = <?php echo json_encode($CurrentProject->rowid); ?>;
        var teamIds = <?php echo json_encode($projectTeamsIds); ?>;
    </script><?php

    require_once VIEWS_PATH."admin/".$tpl;
}
else
{
    header("location:".ROOT_URL."index.php");
}


?>
