<?php 
// import all models
require_once "../../services/header.php";
require "layouts/head.php";

$action = htmlentities(GETPOST('action'));
$projectId = intval(GETPOST('projectId'));
$teamId = intval(GETPOST('teamId'));

$tpl = "map.php";
$errors = array();
$success = false;

if($teamId)
{
    if($projectId)
    {
        $Organization = new Organization($idOrganization);

        // fetching project & team
        foreach($Organization->getProjects() as $Obj)
        {
            if($Obj->getRowid() == $projectId)
            {
                $Project = $Obj;
                break;
            }
        }

        // check if the Team & Project exists
        if(!empty($Project))
        {
            foreach($Project->getTeams() as $Obj)
            {
                // check if the team belongs to this project
                if($Obj->getRowid() == $teamId)
                {
                    $Team = $Obj;
                    break;
                }
            }

            if(!empty($Team))
            {
                if($action == "archiveTeam")
                {
                    if($projectId)
                    {   
                        try {
                            $Team->setActive(0);
                            $Team->update();
                            LogHistory::create($idOrganization, $idUser, "WARNING", 'archive', 'team', $Team->getName(), '', 'team id : '.$Team->getRowid());
                            $success = "Le tableau a bien été archivé.";
                        } catch (\Throwable $th) {
                            $errors[] = "Une erreur innatendue est survenue.";
                            LogHistory::create($idOrganization, $idUser, "ERROR", 'archive', 'team', $Team->getName(), '', 'team id : '.$Team->getRowid(), $th);
                        }
                    }
                    else
                    {
                        $errors[] = "Aucun projet n'a été sélectionné.";
                    }
                }
    
                if($action == "openTeam")
                {
                    if($projectId)
                    {   
                        try {
                            $Team->setActive(1);
                            $Team->update();
                            LogHistory::create($idOrganization, $idUser, "INFO", 'unarchive', 'team', $Team->getName(), '', 'team id : '.$Team->getRowid());
                            $success = "Le tableau a bien été ré-ouvert.";
                        } catch (\Throwable $th) {
                            $errors[] = "Une erreur innatendue est survenue.";
                            LogHistory::create($idOrganization, $idUser, "ERROR", 'unarchive', 'team', $Team->getName(), '', 'team id : '.$Team->getRowid(), $th);
                        }
                    }
                    else
                    {
                        $errors[] = "Aucun projet n'a été sélectionné.";
                    }
                }
            
                if($action == "openProject")
                {
                    if($projectId)
                    {
                        try {
                            $Project->setActive(1);
                            $Project->update();
                            LogHistory::create($idOrganization, $idUser, "WARNING", 'unarchive', 'project', $Project->getName(), '', 'project id : '.$Project->getRowid());
                            $success = "Le projet à bien été ré-ouvert.";
                        } catch (\Throwable $th) {
                            $errors[] = "Une erreur innatendue est survenue.";
                            LogHistory::create($idOrganization, $idUser, "ERROR", 'unarchive', 'project', $Project->getName(), '', 'project id : '.$Project->getRowid(), $th);
                        }
                    }
                    else
                    {
                        $errors[] = "Aucun projet n'a été sélectionné.";
                    }
                }
    
                // for JS
                $username = $Organization->getName();
    
                $authors = array();
                $usernames = array();
    
                // Get tasks authors for JS
                foreach($Team->getUsers() as $User)
                {
                    $usernames[$User->getRowid()] = $User->getLastname() . ' ' . $User->getFirstname();
                }
    
                foreach($Team->getMapColumns() as $columnKey => $Column)
                {
                    foreach($Column->getTasks() as $taskKey => $Task)
                    {
                        // all team users + current admin
                        $TeamUsers = $Team->getUsers();
                        
                        // get all organization admins
                        foreach($Organization->getUsers() as $User)
                        {
                            if($User->isAdmin())
                            {
                                $TeamUsers[] = $User;
                            }
                        }
    
                        // verify that fk_author correspond to an admin user
                        foreach($TeamUsers as $User)
                        {
                            if($User->getRowid() == $Task->getFk_user())
                            {
                                if($User->isAdmin())
                                {
                                    $authors[$columnKey][$taskKey] = $Organization->getName();
                                }
                                else
                                {
                                    $authors[$columnKey][$taskKey] = $usernames[$Task->getFk_user()];
                                }
                                break;
                            }
                        }
                    }
                }
    
                // notification count
                $notificationCount = 0;
                if($Team->isActive() == 0) {
                    $notificationCount++;
                }
                if($Project->isActive() == 0) {
                    $notificationCount++;
                }
    
                ?>
                <script>
                var teamId = <?php echo json_encode($Team->getRowid()); ?>;
                var projectId = <?php echo json_encode($Project->getRowid()); ?>;
                var notificationCount = <?php echo json_encode($notificationCount); ?>;
                const username = <?php echo json_encode($username); ?>;
                const idOrganization = <?php echo json_encode($idOrganization); ?>;
                const idUser = <?php echo json_encode($idUser); ?>;
                </script>
                <?php
            
                require_once VIEWS_PATH."admin/".$tpl;
            }
            else
            {
                header("location:".ROOT_URL."index.php"); 
            }
        }
        else
        {
            header("location:".ROOT_URL."index.php");
        }
    }
    else
    {
        header("location:".ROOT_URL."index.php");
    }
}
else
{
    $errors[] = "Aucune équipe n'a été sélectionnée.";
    $errors = serialize($errors);
    header("location:".CONTROLLERS_URL.'admin/detailsProjet.php?idProject='.$projectId.'&errors='.$errors);
}
?>