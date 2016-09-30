<?php 

/**
 * Class for user registration/login related functions
 */

class User extends Main{
	

	/**
	 * Validates username and password from login/register form input
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	private function validate_user_info($username, $password){
		
		$valid_username = filter_var($username, FILTER_VALIDATE_REGEXP, array("options" => array(
				"regexp" => "/[a-zA-Z0-9_äöüß\-]{5,20}/"
		)));
		
		$valid_pw = filter_var($password, FILTER_VALIDATE_REGEXP, array("options" => array(
				"regexp" => "/[a-zA-Z0-9_äöüß]{8,40}/"
		)));
		
		if(!$valid_username){
			$this->add_error("falscher Benutzername: " . __METHOD__);
			return false;
		}
		else if(!$valid_pw){
			$this->add_error("falsches Passwort" . __METHOD__);
			return false;
		}
		else{
			return true;
		}
	}

/* ===================================================================================== */
	
	/**
	 * Checks if a username exists in the database
	 * @param string $username
	 * @return boolean
	 */
	private function user_exists($username){
		
		$db = new DB();
		 
		$query = "SELECT COUNT(*) FROM users WHERE user_name = ?";
		$params = [$username];
		 
		$existing_rows = $db->countRows($query, $params);
		
		return (intval($existing_rows) > 0);
			
	}
	

/* ===================================================================================== */
	
	/**
	 * Checks if the user's password is correct
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	 private function valid_password($username, $password){
	 	
	 	$db = new DB();
	 	
	 	$query = "SELECT user_password FROM users WHERE username = ?";
	 	$params = [$username];
	 	
	 	$stored = $db->getRow($query, $params);
	 	$stored_pw = $stored['user_password'];
	 	
	 	$match = password_verify($password, $stored_pw);
	 	
	 	if(!$match){
	 		$this->add_error("falsches Passwort" . __METHOD__);
	 		return false;
	 	}else{
	 		return true;
	 	}
	 }

/* ===================================================================================== */
	 
	/**
	 * Sets the $_SESSION variables during login
	 * @param string $username
	 * @param string $user_type
	 */
	private function set_user_session($username, $user_type){
		
		$session_id = uniqid('', true);
		$token = md5($session_id . AUTH_SALT);
		
		//set session id
		$_SESSION['ID'] = $session_id;
			
		//set session auth token
		$_SESSION['token'] = $token;	
		
		//set session user
		$_SESSION['username'] = $username;
				
		//set session user type
		$_SESSION['usertype'] = $user_type;
	}
	 
/* ===================================================================================== */
		
	/**
	 * Registers a new user account
	 * @param array $data The input from the register form
	 */
	public function register_user($data){
		
		$username = $data['username'];
		$pw = $data['password'];
		$pw2 = $data['passwordRepeat'];
		
		$valid_input = $this->validate_user_info($username, $pw);
		
		if(!$valid_input OR $pw != $pw2){
			$this->add_error("ungültige Eingabe");
			return false;
		}
		else if($this->user_exists($username)){
			$this->add_error("Dieser Benutzername wird bereits verwendet. Bitte wählen Sie einen anderen.");
			return false;
		}
		else{
			
			//hash password
			$password = password_hash($pw, PASSWORD_DEFAULT);
			
			//set usertype to 0 (visitor status)
			$usertype = "0";
			
			//insert new user
			$db = new DB();
			$query = "INSERT INTO users (user_name, user_password, user_type) VALUES (?,?,?)";
			$params = [$username, $password, $usertype];
			
			$register = $db->addRow($query, $params);
			
			if(!$register){
				$this->add_error("Eintragen in die Datenbank fehlgeschlagen");
				return false;
			}
			else{
				$this->add_message("Herzlich willkommen, " . $username . ". Ihr Benutzerkonto wurde eingerichtet.");
				return true;
			}
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Logs in a user
	 * @param string username
	 * @param string password
	 * @return boolean
	 */
	public function login_user($username, $password){
				
		//validate input
		$valid_input = $this->validate_user_info($username, $password);
		
		if(!$valid_input){
			$this->add_error("Login fehlgeschlagen");
			return false;
		}
		else{
			//get the user info from the db
			$db = new DB();
			
			$query = "SELECT * FROM users WHERE user_name = ?";
			$params = [$username];
			
			$user_data = $db->getRow($query, $params);
			$stored_pw = $user_data['user_password'];
			
			$password_check = password_verify($password, $stored_pw);
			
			//check if user exists
			if(!$user_data){
				$this->add_error("Benutzername falsch");
				return false;
			}
			elseif(!$password_check){
				$this->add_error("Passwort falsch");
				return false;
			}
			//set user session
			else{
				$set_session = $this->set_user_session($username, $user_data['user_type']);
				$this->add_message("Hallo " . $username . "!");
				return true;
			}	
		}
	}
	
/* ===================================================================================== */
	
	/** 
	 * Verifies the user's login status
	 * @return boolean
	 */
	public function is_logged_in(){
		
		if(!isset($_SESSION['username'])){
			return false;
		}else{
		
			$username = $_SESSION['username'];
			
			//recreate session token with the stored SALT
			$recreate_token = md5($_SESSION['ID'] . AUTH_SALT);
			
			//compare to the current session token
			return ($recreate_token == $_SESSION['token']);
		}	
	}

/* ===================================================================================== */

	/** 
	 * Verifies the user's admin status
	 * @return boolean
	 */
	public function is_admin(){
		
		if($this->is_logged_in()){
			return($_SESSION['usertype'] == "1");
		}else{
			return false;
		}
		
	}
	
/* ===================================================================================== */
		
	/**
	 * Ends the login session
	 */
	public function logout_user(){
		
		session_destroy();
		
	}
	
	
	
	
}