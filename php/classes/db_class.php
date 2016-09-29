<?php 

class DB{
	
	/**
	 * constructor for the database class
	 */
	public function __construct(){
		$this->connect();
	}
	
/* ===================================================================================== */
	
	/**
	 * connects to the database, sets error mode & fetch mode
	 */
	public function connect(){
		try{
			$this->db = new PDO('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			echo 'Verbindung fehlgeschlagen: ' . $e->getMessage();
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * gets a single row from the database
	 * @param string $query
	 * @param array $params
	 * return array
	 */
	public function getRow($query, $params){
		try{
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			return $stmt->fetch();
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}

/* ===================================================================================== */
	
	/**
	 * gets multiple rows from the database
	 * @param string $query
	 * @param array $params
	 * @return array
	 */
	public function getRows($query, $params){
		try{
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			return $stmt->fetchAll();
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}	
	
/* ===================================================================================== */
	
	/**
	 * inserts new row into database
	 * @param string $query
	 * @param array $params
	 * @return boolean
	 */
	public function addRow($query, $params){
		try{
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			return true;
		}catch(PDOException $e){
			echo $e->getMessage();
			return false;
		}
	}	
	
/* ===================================================================================== */
	
	/**
	 * updates row in database
	 * @param string $query
	 * @param array $params
	 * @return boolean
	 */
	public function updateRow($query, $params){
		try{
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			return true;
		}catch(PDOException $e){
			echo $e->getMessage();
			return false;
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * deletes a row from the database
	 * @param string $query
	 * @param array $params
	 * @return boolean
	 */
	public function deleteRow($query, $params){
		try{
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			return true;
		}catch(PDOException $e){
			echo $e->getMessage();
			return false;
		}
	}

/* ===================================================================================== */	

	/**
	 * Gets the number of rows containing a value
	 * @param string $query
	 * @param array $params
	 * @return string
	 */
	public function countRows($query, $params){
		try{
			$stmt = $this->db->prepare($query);
			$stmt->execute($params);
			return $stmt->fetchColumn();
		}catch(PDOException $e){
			echo $e->getMessage();		
		}
	}
	
}