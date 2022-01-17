<?php
//import all models
require_once "../../services/header.php";

$rights = $_SESSION["rights"] ?? false;
$idOrganization = $_SESSION["idOrganization"] ?? false;

if($rights === "admin")
{
    $action = GETPOST('action');
    $idUser = GETPOST('idUser');
    $envoi = GETPOST('envoi');

    $firstname = GETPOST('firstname');
    $lastname = GETPOST('lastname');
    $email = GETPOST('email');
    $idEquipe = GETPOST('idEquipe');
    $birth = GETPOST('birth');

    $oldmdp = GETPOST('oldmdp');
    $newmdp = GETPOST('newmdp');
    $newmdp2 = GETPOST('newmdp2');

    $Organization = new Organization($idOrganization);

    $errors = array();
    $success = false;

    $tpl = "listeMembres.php";

    if($action == "updateFirstname")
    {
        if($idUser && $firstname)
        {
            if($envoi)
            {
                $User = $Organization->fetchUser($idUser);
                $oldFirstname = $User->getFirstname();
            
                if($firstname != $oldFirstname)
                {
                    try
                    {
                        $status = $User->updateFirstname($firstname, $idUser);
                    } 
                    catch (exception $e)
                    {
                        $errors[] = "Le prénom n'a pas pu être modifié.";
                    }

                    if($status)
                    {
                        $success = "Le prénom a bien été modifié.";
                    }
                    else
                    {
                        $errors[] = "Le prénom n'a pas pu être modifié.";
                    }

                } 
                else 
                {
                    $errors[] = "Le nom est le même qu'avant.";
                }
            } 
            else 
            {
                header("location:".ROOT_URL."/index.php");
            }
        } 
        else 
        {
            header("location:".ROOT_URL."/index.php");
        }
    }
    
    if($action == "updateLastname")
    {
        if($idUser && $lastname)
        {
            if($envoi)
            {
                $User = $Organization->fetchUser($idUser);
                $oldLastname = $User->getLastname();
            
                if($lastname != $oldLastname)
                {
                    try
                    {
                        $User->updateLastname($lastname, $idUser);
                    } 
                    catch (exception $e)
                    {
                        $errors[] = "La modification de nom n'a pas pu aboutir.";
                    }
                    $success = "Le nom a bien été modifié.";
                } 
                else 
                {
                    $errors[] = "Le nom n'a pas été modifié.";
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

    if($action == "deleteUser")
    {
        if($idUser)
        {
            $User = $Organization->fetchUser($idUser);
            $status = $User->delete($idUser);

            if($status)
            {
                $success = "La suppression d'utilisateur a bien été effectuée.";
            }
            else
            {
                $errors[] = "La suppression d'utilisateur n'a pas pu aboutir.";
            }
        } 
        else
        {
            header("location:".ROOT_PATH."index.php");
        }
    }


    // refresh datas if modifications in db
    if($success)
    {
        $Organization = new Organization($idOrganization);
    }

    require_once VIEWS_PATH."admin/".$tpl;

}
else
{
    header("location:".ROOT_URL."index.php");
}

