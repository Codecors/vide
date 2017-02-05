<?php 

class Settings extends Main{
	
	public $file_size_limit;
	
	public function __construct(){
		
		parent::__construct();
		
		$this->file_size_limit = ini_get('upload_max_filesize');
		
	}
	
/* ===================================================================================== */
	
	/**
	 * Validates settings form input
	 * @param array $input
	 * return boolean
	 */
	private function validate_settings($input){
		
		$valid_video_formats = str_replace(' ', '', $input['allowed_video_formats']);
		$valid_video_formats = filter_var($valid_video_formats, FILTER_VALIDATE_REGEXP, array("options" => array(
			"regexp" => "/[a-zA-Z0-9\,]/"	
		)));
		
		$valid_img_formats = str_replace(' ', '', $input['allowed_img_formats']);
		$valid_img_formats = filter_var($valid_img_formats, FILTER_VALIDATE_REGEXP, array("options" => array(
				"regexp" => "/[a-zA-Z0-9\,]/"
		)));
		
		//check against max file size
		if($input['max_video_size'] >= $this->file_size_limit || $input['max_img_size'] >= $this->file_size_limit){
			$this->add_error(__METHOD__, "Die maximale Dateiuploadgröße beträgt " . $this->file_size_limit);
			return false;
		}
		else if(!$valid_img_formats || !$valid_img_formats){
			$this->add_error(__METHOD__, "Fehler bei der eingabe der Dateiformate");
			return false;
		}
		else{
			return true;
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Sanitizes output from settings table
	 * @param array $data
	 * @return array 
	 */
	private function sanitize_settings($data){
		
		$clean_video_file_size = intval($data['max_video_file_size']);
		$clean_img_file_size = intval($data['max_img_file_size']);
		$clean_video_formats = filter_var($data['allowed_video_formats'], FILTER_VALIDATE_REGEXP, array("options" => array(
				"regexp" => "/[a-zA-Z0-9\,]/"
		)));
		$clean_video_formats = filter_var($data['allowed_img_formats'], FILTER_VALIDATE_REGEXP, array("options" => array(
				"regexp" => "/[a-zA-Z0-9\,]/"
		)));
		
		$clean_data = array(
				'max_video_file_size' => $clean_video_file_size,
				'max_img_file_size' => $clean_img_file_size,
				'allowed_video_formats' => $clean_video_formats,
				'allowed_img_formats' => $clean_img_formats
		);
	}
	
/* ===================================================================================== */
	
	/**
	 * Get the current settings
	 * @return array
	 */
	public function get_settings(){
		$db = new DB();
		$query = "SELECT * FROM settings";
		$params = [];
		
		$settings = $db->getRows($query, $params);
		
		if(!$settings){
			$this->add_error(__METHOD__, "Fehler beim Lesen der Einstellungen");
			return false;
		}else{
		
			$sanitized_settings = $this->sanitize_settings($settings);
			
			return $sanitized_setting;
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Enters changed values into settings table
	 * @param array $input
	 * @param boolean $is_admin
	 * @return boolean
	 */
	public function change_settings($input, $is_admin){
		
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{
		
			$valid = $this->validate_settings($input);
			
			if(!$valid){
				$this->add_error(__METHOD__, "ungültige Eingabe");
				return false;
			}else{
				$db = new DB();
				
				$query = "UPDATE settings SET 
						max_video_file_size = ?, 
						max_img_file_size = ?,
						allowed_video_formats = ?,
						allowed_img_formats = ?
						";
				
				$params = [
						$input['max_video_file_size'],
						$input['max_img_file_size'],
						$input['allowed_video_formats'],
						$input['allowed_img_formats']
				];
				
				$update = $db->updateRow($query, $params);
				
				if(!$update){
					$this->add_error(__METHOD__, "Beim Speichern der neuen Werte ist ein Fehler aufgetreten");
					return false;
				}else{
					$this->add_message("Einstellungen erfolgreich geändert");
					return true;
				}
			}
		
		}
	}
}