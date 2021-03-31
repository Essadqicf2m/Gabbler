<?php

class UserManager extends ManagerTableAbstract implements ManagerTableInterface
{

    //put your query for selected all datas in this table
    public function selectAll(): array
    {
        $sql = "SELECT * FROM user;";
        $query = $this->db->query($sql);
        // if we have at least one result
        if ($query->rowCount()) {
            return $query->fetchAll(PDO::FETCH_ASSOC);
        }
        // else an empty array
        return [];
    }

    // Checks the user's connection in the DB and retrieves the necessary parameters to create the session
    public function signIn(User $user): array {
        $query = "SELECT u.`nickname_user`,u.`pwd_user` 
	      FROM user u
	      LEFT JOIN role_has_user h ON u.id_user = h.role_has_user_id_user
	      LEFT JOIN role r ON r.id_role = h.role_has_user_id_role
	      WHERE nickname_user = ? AND pwd_user = ? ;";
        $req = $this->db->prepare($query);
        $req->bindValue(1,$user-getNicknameUser(),PDO::PARAM_STR);
        $req->bindValue(2,$user->getPwdUser(),PDO::PARAM_STR);
        try{
            $req->execute();
            if($req->rowCount()){
                $_SESSION = $req->fetch(PDO::FETCH_ASSOC);
                $_SESSION['sessionId'] = session_id();
                return true;
            }else{
                return false;
            }
        }catch (PDOException $e){
            return $e->getMessage();
        }


        // call $this->verifyPassword()
        // call $this->createSession()
    }
    // Disconnecting from the session

    public static function signOut(User $user): bool {

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        return true;

    }

    // Allows you to create a new user, if inserted, an email must be sent to him with a confirmation link containing his id and his unique key
    public function signUp(User $user): array
    {
        $cryptPassword = $this->cryptPassword($user->getPwdUser());
        $signUpValidationKey = $this->signUpValidationKey();

        $sql = "INSERT INTO user (nickname_user, pwd_user, mail_user, color_user, confirmation_key_user) VALUES (?,?,?,?,?)";
        $prepare = $this->db->prepare($sql);
        try {
            $prepare->execute([
                $user->getNicknameUser(),
                $cryptPassword,
                $user->getMailUser(),
                '{"background":"#f6f6f6","color":"#505352"}',
                $signUpValidationKey
            ]);
        } catch (Exception $e) {
            trigger_error($e->getMessage());
        }
    }

    // When clicking from the mailbox with a confirmation link containing its nickname_user and its unique key, the validation field is updated by mail

    public function registrationUpdateUser(string $nickname, string $confirmationKey): bool {
        $query = "UPDATE user SET validation_status_user = 1 WHERE nickname_user = ? AND confirmation_key_user = ?;";
        $prepare = $this->db->prepare($query);
        $prepare->bindValue(1,$nickname, PDO::PARAM_STR);
        $prepare->bindValue(2,$confirmationKey,PDO::PARAM_STR);
        return $prepare->execute();

    }

    // Create the session with the values coming from signIn ()
    protected function createSession(array $datas): bool
    {

    }

    // Allows you to create a random character string of up to 60 characters
    protected function signUpValidationKey(): string
    {

    }

    // crypt password with password_hash

    protected function cryptPassword(string $pwd): string {
        return password_hash($pwd,PASSWORD_DEFAULT);
    }

    // verify password crypted (password_hash) with password_verify
    protected function verifyPassword(string $cryptPwd, string $pwd): bool {
        return password_verify($pwd,$cryptPwd);

    }

}
