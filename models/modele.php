<?php
class Modele 
{
    public function getBdd()
    {
        // INITIALISATION DE LA CONNEXION A LA BDD
        return new PDO('mysql:host=localhost;dbname=storieshelper;charset=UTF8', 'root');
    }

}
?>