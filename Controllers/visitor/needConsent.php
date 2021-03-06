<?php 
//import all models
require_once "../../services/header.php";
require "layouts/head.php";

if($rights === 'needConsent' && $idUser)
{
    $action = GETPOST('action');
    
    $User = new User($idUser);
    $Organization = new Organization();
    
    $errors = array();
    $success = false;
    
    $tpl = "needConsent.php";
    $page = "controllers/visitor/".$tpl;

    if($action == "refuseConsent")
    {
        // DELETE ACCOUNT
        $status = $User->delete();
        LogHistory::create($idUser, 'delete', 'user', $idUser, null, null, null, null, $User->getFk_organization(), "INFO", null, $ip, $page);

        header('location:'.CONTROLLERS_URL.'visitor/signout.php');
        exit;
    }

    if($action == "giveConsent")
    {
        try {
            $User->setConsentDate(date('Y-m-d H:i:s'));
            $User->setConsent(true);
            $User->update();

            $_SESSION["rights"] = $User->isAdmin() ? 'admin' : 'user';
            LogHistory::create($idUser, 'consent', 'user', $idUser, null, null, null, null, $User->getFk_organization(), "INFO", null, $ip, $page);

            header('location:'.ROOT_URL.'index.php');
            exit;
        } catch (\Throwable $th) {
            //throw $th;
            $errors[] = "Une erreur innatendue est survenue.";
        }
    }

    require_once VIEWS_URL.'visitor/'.$tpl;
}
else
{
    header('location:'.ROOT_URL.'index.php');
}
?>