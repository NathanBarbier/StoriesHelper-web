<?php
class Organization extends Modele
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $teams = [];
    private $users = [];
    private $positions = [];
    private $projects = [];

    public function __construct($id = null)
    {
        if($id != null)
        {
            $sql = "SELECT * ";
            $sql .= " FROM organizations"; 
            $sql .= " WHERE rowid = ?";

            $requete = $this->getBdd()->prepare($sql);
            $requete->execute([$id]);

            $Organization = $requete->fetch(PDO::FETCH_OBJ);
            
            $this->id = $id;
            $this->name = $Organization->name;
            $this->email = $Organization->email;

            $Team = new Team();
            $teams = $Team->fetchAll($this->id);

            foreach($teams as $team)
            {
                $obj = new Team($team->rowid);
                $this->teams[] = $obj; 
            }

            $User = new User();
            $users = $User->fetchAll($this->id);

            foreach($users as $user)
            {
                $obj = new User($user->rowid);
                $this->users[] = $obj;
            }

            $Position = new Position();
            $positions = $Position->fetchAll($this->id);

            // var_dump($positions);
            // exit;

            foreach($positions as $position)
            {
                $obj = new Position($position->rowid);
                $this->positions[] = $obj;
            }

            $Project = new Project();
            $projects = $Project->fetchAll($this->id);
            foreach($projects as $project)
            {
                $obj = new Project($project->rowid);
                $this->projects[] = $obj; 
            }
        }
    }


    //! SETTER

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }


    //! GETTER
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getTeams()
    {
        return $this->teams;
    }

    public function countTeams()
    {
        return count($this->teams);
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function countUsers()
    {
        return count($this->users);
    }

    public function getPositions()
    {
        return $this->positions;
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function getPositionNameByUserId($UserId)
    {
        foreach($this->getUsers() as $user)
        {
            if($user->getIdUser() == $UserId)
            {
                $userPositionId = $user->getUserPositionId();
            }
        }

        foreach($this->getPositions() as $position)
        {
            if($position->getPositionId() == $userPositionId)
            {
                return $position->getPositionName();
            }
        }
    }

    public function getPositionInfosByPositionId($positionId)
    {
        $infosPoste = [];
        foreach($this->getPositions() as $Position)
        {
            if($Position->idPoste == $positionId)
            {
                $positionInfos = new stdClass;

                $positionInfos->id = $Position->rowid;
                $positionInfos->name = $Position->name;
                $positionInfos->idOrganization = $Position->fk_organization;
                $positionInfos->idRole = $Position->fk_role;
            }
            return $positionInfos;
        }
    }

    public function getOrgUsersInfos()
    {
        $usersInfos = array();
        foreach($this->getUsers() as $user)
        {
            $userInfos  = new stdClass;

            $userInfos->id = $user->getId();
            $userInfos->idPosition = $user->getPositionId();
            $userInfos->idTeam = $user->getTeamId();
            $userInfos->idOrganization = $user->getOrganizationId();
            $userInfos->firstname = $user->getFirstname();
            $userInfos->lastname = $user->getLastname();
            $userInfos->email = $user->getEmail();
            $userInfos->birth = $user->getBirth();
            $userInfos->password = $user->getPassword();
            $userInfos->teamName = $this->getTeams()[$user->getUserTeamId()]->getName();
            $userInfos->positionName = $this->getPositions()[$user->getUserPositionId()]->getName();

            $usersInfos[] = $userInfos;
        }
        return $usersInfos;
    }

    public function getUsersByTeamId($teamId)
    {
        $usersEquipe = [];
        foreach($this->getTeams() as $equipe)
        {
            if($equipe->getIdEquipe() == $teamId)
            {
                foreach($equipe->getMembresEquipe() as $utilisateur)
                {
                    $usersEquipe[] = [
                        "idUtilisateur" => $utilisateur->getIdUser(),
                        "nom" => $utilisateur->getNomUser(),
                        "prenom" => $utilisateur->getPrenomUser(),
                        "dateNaiss" => $utilisateur->getDateNaissUser(),
                        "mdp" => $utilisateur->getMdpUser(),
                        "idPoste" => $utilisateur->getIdPosteUser(),
                        "email" => $utilisateur->getEmailUser(),
                        "idEquipe" => $utilisateur->getIdEquipeUser(),
                        "idorganization" => $utilisateur->getIdorganizationUser(),
                    ];
                }
                return $usersEquipe;
            }
        }
    }

    public function getUsersByPoste($idPoste)
    {
        $usersPoste = [];
        foreach($this->getPositions() as $position)
        {
            if($position->getIdEquipe() == $idPoste)
            {
                foreach($position->getMembresPoste() as $utilisateur)
                {
                    $usersEquipe[] = [
                        "idUtilisateur" => $utilisateur->getIdUser(),
                        "nom" => $utilisateur->getNomUser(),
                        "prenom" => $utilisateur->getPrenomUser(),
                        "dateNaiss" => $utilisateur->getDateNaissUser(),
                        "mdp" => $utilisateur->getMdpUser(),
                        "idPoste" => $utilisateur->getIdPosteUser(),
                        "email" => $utilisateur->getEmailUser(),
                        "idEquipe" => $utilisateur->getIdEquipeUser(),
                        "idorganization" => $utilisateur->getIdorganizationUser(),
                    ];
                }
                return $usersPoste;
            }
        }
    }

    public function getMinMaxIdTeam()
    {
        $extremes = new stdClass;
        foreach($this->getTeams() as $cle => $Team)
        {
            $teamId = $Team->getId();
            if($cle == 0)
            {
                $extremes->minIdE = $teamId;
                $extremes->maxIdE = $teamId;
            }
            else
            {
                if($teamId > $extremes->maxIdE)
                {
                    $extremes->maxIdE = $teamId;
                }
                if($teamId < $extremes->minIdE)
                {
                    $extremes->minIdE = $teamId;
                }
            }
            
        }
        return $extremes;
    }

    public function getMaxIdUser()
    {
        foreach($this->getUsers() as $cle => $user)
        {
            $idUser = $user->getIdUser();
            $maxIdUser = null;
            if ($cle == 0)
            {
                $maxIdUser = $user->getIdUser();
            }
            else
            {
                if($maxIdUser < $idUser)
                {
                    $maxIdUser = $idUser;
                }
            }
        }

        return $maxIdUser;
    }

    public function getMaxIdPosition()
    {
        foreach($this->getPositions() as $cle => $position)
        {
            $idPoste = $position->getId();
            $maxIdPoste = null;
            if($cle == 0)
            {
                $maxIdPoste = $position->getIdPoste();
            }
            else
            {
                if($maxIdPoste < $idPoste)
                {
                    $maxIdPoste = $idPoste;
                }
            }
        }
        return $maxIdPoste;
    }

    public function getMaxIdProject()
    {
        foreach($this->getProjects() as $key => $project)
        {
            $projectId = $project->getIdProjet();
            $projectMaxId = null;
            if($cle == 0)
            {
                $projectMaxId = $project->getIdProjet();
            } 
            else 
            {
                if($projectMaxId < $projectId)
                {
                    $projectMaxId = $projectId;
                }
            }
        }
        return $projectMaxId;
    }

    public function CountUsersByTeams()
    {
        $nbUsersParEquipe = [];
        foreach($this->getTeams() as $Team)
        {
            $nbUsers = $Team->countMembres();
                
            $idE = $Team->getId();
            $nbUsersParEquipe[$idE] = $nbUsers;
        }
        return $nbUsersParEquipe;
    }

    public function CountUsersByPosition()
    {
        $nbUsersParPoste = [];
        foreach($this->getPositions() as $Position)
        {
            if(!empty($this->getUsers()))
            {
                foreach($this->getUsers() as $user)
                {
                    $users = [];
                    if($user->getIdPosition() == $Position->getId())
                    {
                        $users[] = $user->getId();
                    }
                    $nbUsers = count($users);
                    $nbUsersParPoste[$Position->getId()] = $nbUsers;
                }
            }
            else
            {
                $nbUsersParPoste[$Position->getId()] = 0;
            }
        }
        return $nbUsersParPoste;
    }


    //! UPDATE

    public function updateName($name)
    {
        $sql = "UPDATE organizations"; 
        $sql .= " SET nom = ?";
        $sql .= " WHERE idorganization = ?";

        $requete = $this->getBdd()->prepare($sql);
        return $requete->execute([$name, $this->id]);
    }

    public function updateEmail($email)
    {
        $sql = "UPDATE organizations"; 
        $sql .= " SET email = ?";
        $sql .= " WHERE idorganization = ?";

        $requete = $this->getBdd()->prepare($sql);
        return $requete->execute([$email, $this->id]);
    }

    public function updateMdp($mdp)
    {
        $sql = "UPDATE organizations"; 
        $sql .= " SET password = ?";
        $sql .= " WHERE idorganization = ?";

        $requete = $this->getBdd()->prepare($sql);
        return $requete->execute([$mdp, $this->id]);
    }


    //! DELETE 

    public function delete()
    {
        $sql = "DELETE FROM work_to AS w";
        $sql .= " INNER JOIN teams AS t ON w.fk_team = t.rowid";
        $sql .= " WHERE fk_organization = ?";
        
        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->id]);
    
        $sql = "DELETE FROM projects"; 
        $sql .= " WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->id]);
    
        $sql = "DELETE FROM users"; 
        $sql .= " WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->id]);
        
        $sql = "DELETE FROM teams";
        $sql .= " WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->id]);
        
        $sql = "DELETE FROM positions";
        $sql .= " WHERE fk_organization = ?";

        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->id]);
        
        $sql = "DELETE FROM organizations";
        $sql .= " WHERE rowid = ?";
        
        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$this->id]);
    
        session_destroy();
    }


    //! FETCH

    public function fetchByEmail($email)
    {
        $sql = "SELECT *";
        $sql .= " FROM organizations"; 
        $sql .= " WHERE email = ?";
        
        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$email]);

        return $requete->fetch(PDO::FETCH_OBJ);
    }


    //! METHODES

    public function checkByName($name)
    {
        $sql = "SELECT *";
        $sql .= " FROM organizations";
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

    public function checkByEmail($email)
    {
        $sql = "SELECT * ";
        $sql .= " FROM organizations";
        $sql .= " WHERE email = ?";
        
        $requete = $this->getBdd()->prepare($sql);
        $requete->execute([$email]);

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