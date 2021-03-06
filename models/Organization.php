<?php
class Organization extends Modele
{
    protected ?int      $rowid      = null;
    protected string    $name;
    protected array     $users      = array();
    protected array     $projects   = array();
    protected array     $logs       = array();
    protected int       $privacy    = 0;

    /**
     * @param int rowid The Organization id
     * @param int privacy The level of privacy of the recovered informations | 0 = fetch all | 1 = fetch user emails | 2 = max privacy level
     */
    public function __construct(int $rowid = null, int $privacy = 2)
    {
        if($rowid != null)
        {
            $this->rowid    = $rowid;
            $this->privacy  = $privacy;
            $this->fetch();
        }
    }

    // SETTER

    public function setRowid(int $rowid)
    {
        $this->rowid = $rowid;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setPrivacy($privacy)
    {
        $this->privacy = $privacy;
    }

    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    public function addProject(Project $project)
    {
        $this->projects[] = $project;
    }

    // GETTER
    public function getRowid()
    {
        return $this->rowid;
    }

    public function getName()
    {
        return $this->name;
    }


    public function getUsers()
    {
        return $this->users;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function getLogs()
    {
        return $this->logs;
    }

    public function removeUser(int $fk_user)
    {
        foreach($this->users as $key => $User)
        {
            if($User->getRowid() == $fk_user)
            {
                unset($this->users[$key]);
            }
        }
    }

    public function removeProject(int $fk_project)
    {
        foreach($this->projects as $key => $Project)
        {
            if($Project->getRowid() == $fk_project)
            {
                unset($this->projects[$key]);
            }
        }
    }

    // CREATE

    public function create()
    {
        $sql = "INSERT INTO storieshelper_organization (name)";
        $sql .= " VALUES (?)";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->name]);

        $sql = "SELECT MAX(rowid) AS rowid FROM storieshelper_organization";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute();

        if($requete->rowCount() > 0)
        {
            $obj = $requete->fetch(PDO::FETCH_OBJ);

            return intval($obj->rowid);
        }
        else
        {
            return false;
        }
    }


    // UPDATE

    public function update()
    {
        $sql = "UPDATE storieshelper_organization"; 
        $sql .= " SET name = ?";
        $sql .= " WHERE rowid = ?";

        $requete = $this->getBdd()->prepare($sql);
        return $requete->execute([$this->name, $this->rowid]);
    }

    // DELETE 

    public function delete()
    {
        // delete organization
        $sql = "DELETE FROM storieshelper_organization WHERE rowid = ?;";

        $requete = $this->getBddSafe()->prepare($sql);
        $requete->execute([$this->rowid]);

        // check if a session exists
        if(session_status() == PHP_SESSION_ACTIVE)
        {
            session_destroy();
        }
    }


    // FETCH

    public function fetch()
    {
        $sql = "SELECT *";
        $sql .= " FROM storieshelper_organization";
        $sql .= " WHERE rowid = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $obj = $requete->fetch(PDO::FETCH_OBJ);
            
            $this->rowid    = intval($obj->rowid);
            $this->name     = $obj->name;

            $this->fetchUsers();
            $this->fetchProjects();

            if($this->privacy == 0) {
                $this->fetchLogs();
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }

    public function fetchAllUsers()
    {
        $sql = "SELECT * FROM storieshelper_user WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $lines = $requete->fetchAll(PDO::FETCH_OBJ);

            foreach($lines as $line)
            {
                $User = new User();
                $User->initialize($line);
                $this->users[] = $User;
            }
        }
    }

    /**
     * Fetch the 30 first organization users and affect them to the Organization $users property
     */
    public function fetchUsers()
    {
        $idUser = !empty($_SESSION['idUser']) ? $_SESSION['idUser'] : 0;

        // position the current user at the top of the list
        if($idUser) 
        {
            $sql = "SELECT * FROM storieshelper_user WHERE rowid = ?";

            $requete = $this->getBdd()->prepare($sql);
            $requete->execute([$idUser]);

            if($requete->rowCount() > 0)
            {
                $obj = $requete->fetch(PDO::FETCH_OBJ);

                $User = new User();
                $User->initialize($obj);
                $this->users[] = $User;
            }
        }

        $sql = "SELECT *";
        $sql .= " FROM storieshelper_user";
        $sql .= " WHERE fk_organization = ?";
        $sql .= " ORDER BY lastname, firstname";
        $sql .= " LIMIT 30";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);
         
        if($requete->rowCount() > 0)
        {
            $lines = $requete->fetchAll(PDO::FETCH_OBJ);

            foreach($lines as $line)
            {
                if($line->rowid != $idUser)
                {
                    $User = new User();
                    $User->initialize($line, $this->privacy);
                    $this->users[] = $User;
                }
            }
        }
    }

    public function fetchAllAdmins()
    {
        $sql = "SELECT * FROM storieshelper_user WHERE fk_organization = ? AND admin = 1";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $lines = $requete->fetchAll(PDO::FETCH_OBJ);

            foreach($lines as $line)
            {
                $User = new User();
                $User->initialize($line, 3);
                $this->users[] = $User;
            }
        }
    }

    public function fetchUsersCount()
    {
        $sql = "SELECT COUNT(*) AS count FROM storieshelper_user";
        $sql .= " WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $obj = $requete->fetch(PDO::FETCH_OBJ);
            return intval($obj->count);
        }
    }

    /**
     * Fetch all organization projects and affect them to the Organization $projects property
     * @param int $depth The depth of loading of children properties of projects |
     * load : 
     * 0 = projects | 1 = teams | 2 = columns | 3 = |
     */
    public function fetchProjects(int $depth = 2)
    {
        $sql = "SELECT *";
        $sql .= " FROM storieshelper_project";
        $sql .= " WHERE fk_organization = ?";
        $sql .= " ORDER BY open DESC, name ASC";
        $sql .= " LIMIT 10";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $lines = $requete->fetchAll(PDO::FETCH_OBJ);
        
            foreach($lines as $line)
            {
                $Project = new Project();
                $Project->initialize($line, $depth);
                $this->projects[] = $Project;
            }
        }
    }

    /** 
     * Fetch all organization logs and affect them to the Organization $logs property
     */
    public function fetchLogs()
    {
        $sql = "SELECT *";
        $sql .= " FROM storieshelper_log_history";
        $sql .= " WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $lines = $requete->fetchAll(PDO::FETCH_OBJ);

            foreach($lines as $line)
            {
                $LogHistory = new LogHistory();
                $LogHistory->initialize($line);

                $this->logs[] = $LogHistory;
            }
        }
    }

    /**
     * Return the matching user in the Organization users property
     * @param int $fk_user 
     * @return User $User the user matching id user
     */
    public function getUser(int $fk_user)
    {
        foreach($this->users as $User)
        {
            if($User->getRowid() == $fk_user)
            {
                return $User;
            }
        }
        return false;
    }

    /**
     * Fetch a user relating to the organization and had it to the users property
     */
    public function fetchUser(int $fk_user)
    {
        $sql = "SELECT * FROM storieshelper_user";
        $sql .= " WHERE fk_organization = ? AND rowid = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid, $fk_user]);

        if($requete->rowCount() > 0)
        {
            $obj = $requete->fetch(PDO::FETCH_OBJ);

            $User = new User();
            $User->initialize($obj);

            if(!empty($User))
            {
                $this->users[] = $User;
            }
        }
    }

    public function fetchName()
    {
        $sql = "SELECT name FROM storieshelper_organization WHERE rowid = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->rowid]);

        if($requete->rowCount() > 0)
        {
            $obj = $requete->fetch(PDO::FETCH_OBJ);
            $this->name = $obj->name;
        }

    }

    public function fetch_last_insert_id()
    {
        $sql = "SELECT MAX(rowid) as rowid";
        $sql .= " FROM storieshelper_organization";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute();

        return intval($requete->fetch(PDO::FETCH_OBJ)->rowid);
    }


    // METHODES

    public function checkByName($name)
    {
        $sql = "SELECT *";
        $sql .= " FROM storieshelper_organization";
        $sql .= " WHERE name = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$name]);

        if($requete->rowCount() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }

    }
}
?>