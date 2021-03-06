<?php
class BelongsTo extends Modele
{
    protected ?int $fk_user = null;
    protected ?int $fk_team = null;

    function __construct(int $fk_user = null, int $fk_team = null)
    {
        if($fk_user != null && $fk_team != null)
        {
            $sql = "SELECT *";
            $sql .= " FROM storieshelper_belong_to";
            $sql .= " WHERE fk_user = ? AND fk_team = ?";

            $requete = $this->getBdd()->prepare($sql);
            $requete->execute([$fk_user, $fk_team]);

            if($requete->rowCount() > 0)
            {
                $obj = $requete->fetch(PDO::FETCH_OBJ);
                $this->fk_user = $obj->fk_user; 
                $this->fk_team = $obj->fk_team; 
            }
        }
    }
    
    // GETTER
    
    public function getFk_user()
    {
        return $this->fk_user;
    }
    
    public function getFk_team()
    {
        return $this->fk_team;
    }

    // SETTER

    public function setFk_user(int $fk_user)
    {
        $this->fk_user = $fk_user;
    }

    public function setFk_team(int $fk_team)
    {
        $this->fk_team = $fk_team;
    }
    
    // INSERT

    public function create()
    {
        $sql = "INSERT INTO storieshelper_belong_to (fk_user, fk_team)"; 
        $sql .= " VALUES (?, ?)";

        $requete = $this->getBdd()->prepare($sql);
        return $requete->execute([$this->fk_user, $this->fk_team]);
    }

    // DELETE

    public function delete()
    {
        $sql = "DELETE FROM storieshelper_belong_to";
        $sql .= " WHERE fk_user = ? AND fk_team = ?";

        $requete = $this->getBddSafe()->prepare($sql);
        return $requete->execute([$this->fk_user, $this->fk_team]);
    }

    // METHODS

    /** Return all associated teams with the user
     * @param int $fk_user The user for which we will search for the related teams
     * @return Array<BelongTo> All associated teams with the user
     */
    public function fetchAll(int $fk_user)
    {
        $sql = "SELECT fk_user, fk_team";
        $sql .= " FROM storieshelper_belong_to";
        $sql .= " WHERE fk_user = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$fk_user]);

        $BelongsTos = array();

        if($requete->rowCount() > 0)
        {
            $lines = $requete->fetchAll(PDO::FETCH_OBJ);
    
            foreach($lines as $line)
            {
                // $BelongsTos[] = new BelongsTo($line->fk_user, $line->fk_team);
                $BelongsTo = new BelongsTo();
                $BelongsTo->fk_team = $line->fk_team;
                $BelongsTo->fk_user = $line->fk_user;
                $BelongsTos[] = $BelongsTo;
            }
        }

        return $BelongsTos;
    }
}

?>