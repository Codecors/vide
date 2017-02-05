<?php 

class Video extends Main{
	
	/**
	 * Validates a video id
	 * @param mixed $video_id
	 * @return boolean
	 */
	private function valid_id($id){
		
		if($id == intval($id) && $id < 5000){
			return true;
		}
		else{
			return false;
		}	
	}
	
/* ===================================================================================== */

	/**
	 * Validates video data from upload or edit form input
	 * @param array $video_data
	 * @return boolean
	 */
	private function validate_video_data($video_data){
		
		$valid_id = $this->valid_id($video_data['video_id']);
		
		$valid_title = filter_var($video_data['video_title'], FILTER_VALIDATE_REGEXP, array("options" =>
				array("regexp" => "/[a-zA-Zäöüß0-9\s\-\_\&]{1,50}/")
		));
		
		if(strlen($video_data['video_desc']) <= 300 && $valid_id && $valid_title){
			return true;
		}
		else{
			$this->add_error(__METHOD__, "ungültige Eingabe");
			return false;
		}
	}

/* ===================================================================================== */
	
	/**
	 * sanitized database output for display
	 * @param array $output Database output array
	 * @return array
	 */
	private function sanitize_video_data($output){
		
 		$video_id = intval($output['video_id']);
		$video_title = htmlspecialchars($output['video_title'], ENT_QUOTES);
		$video_desc = htmlspecialchars($output['video_desc'], ENT_QUOTES);
		$video_rating = intval($output['video_rating']);
		$video_file = htmlspecialchars($output['video_file']);
		$thumbnail_file = htmlspecialchars($output['thumbnail_file']);
		$video_upload = intval($output['video_public']);
		$upload_date = preg_replace("([^0-9 \.])", "", $output['video_upload_date']);
		$video_type = htmlentities($output['video_type']);
		
		$clean_output = array(
 				'video_id' => $video_id,
				'video_title' => $video_title,
				'video_desc' => $video_desc,
				'video_rating' => $video_rating,
				'video_file' => $video_file,
				'thumbnail_file' => $thumbnail_file,
				'video_public' => $video_upload,
				'video_upload_date' => $upload_date,
				'video_type' => $video_type
		);
		
		return $clean_output;
	}
	
/* ===================================================================================== */
	
	/**
	 * Fetches data for a single video from the db
	 * @param mixed $video_id
	 * @param boolean $is_logged_in
	 * @return array Associative array of video data
	 */
	public function get_video_data($video_id, $login){
		
		$db = new DB();
		
		if($this->valid_id($video_id)){
			
			$query = "SELECT * FROM videos WHERE video_id = ?";
			
			$params = [$video_id];
			
			$data = $db->getRow($query, $params);
			
			if(!$data){
				$this->add_error(__METHOD__, "Abrufen der Information für Video Nr. " . $video_id . " fehlgeschlagen");
				return false;
			}
			elseif($data["video_public"] != "1" && $login == false){
				$this->add_error(__METHOD__, "Login erforderlich");
				return false;
			}
			else{
				$clean_data = $this->sanitize_video_data($data);
				
				$json = json_encode($clean_data, JSON_UNESCAPED_UNICODE);
				
				return $json;
			}
		}
		else{
			$this->add_error(__METHOD__, "ungültige Eingabe");
		}	
	}
	
/* ===================================================================================== */

	/**
	 * gets data for all videos or all public videos from db
	 * @param boolean $is_admin
	 * @return string
	 */
	public function get_video_list($is_admin){
		
		$db = new DB();
		
		if($is_admin){
			$query = "SELECT * FROM videos";
			$params = [];
		}else{
			$query ="SELECT * FROM videos WHERE video_public = ?";
			$params = ["1"];
		}
		$videos = $db->getRows($query, $params);
		
		if(empty($videos)){
			$this->add_message("Keine Videos anzuzeigen");
			return false;
		}else{
			
			$output = array();
			
			foreach($videos as $video){
				
				$clean_data = $this->sanitize_video_data($video);
				
				array_push($output, $clean_data);
			}
			
			$json = json_encode($output, JSON_UNESCAPED_UNICODE, 2);
		}
		
		return $json;
	}
	
/* ===================================================================================== */
	
	/**
	 * Updates db entry for a video
	 * @param array $new_data The edit form data sent via ajax
	 * @param boolean $is_admin
	 * @return boolean 
	 */
	public function edit_video($new_data,  $is_admin){
				
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{
		
			$valid = $this->validate_video_data($new_data);
			
			if(!$valid){
				$this->add_error(__METHOD__, "Fehler bei der Eingabe");
				return false;
			}
			else{
				
				$video_id = $new_data['video_id'];
				
				$new_title = $new_data['video_title'];
				
				$new_desc = $new_data['video_desc'];
				$new_desc = htmlspecialchars($new_desc);
				
				$db = new DB();
				$query = "UPDATE videos SET video_title = ?, video_desc = ? WHERE video_id = ?";
				$params = [$new_title, $new_desc, $video_id];
				
				$update = $db->updateRow($query, $params);
				
				if($update){
					$this->add_message("Videodaten erfolgreich gespeichert");
					return true;
				}
				else{
					$this->add_error(__METHOD__, "Eintrag in die Datenbank fehlgeschlagen");
					return false;
				}
			}
		}	
	}
	
/* ===================================================================================== */
	
	/**
	 * Changes the rating for a video
	 * @param array $input Form input array
	 * @param boolean $is_logged_in
	 * @return boolean
	 */	
	public function change_rating($input, $is_logged_in){
		
		$video_id = $input['video_id'];
		$new_rating = $input['new_rating'];
				
		$valid_video = $this->valid_id($video_id);
		$valid_rating = (intval($new_rating) < 6);
				
		if(!$is_logged_in){
			$this->add_error(__METHOD__, "Login benötigt");
			return false;
		}else if(!$valid_id OR !$valid_rating){
			return false;
		}else{
			$user_id = $_SESSION['user_id'];
			$time = date("Y-m-d H:i:s");
			
			//	check to see if rated by this user before
			$db = new DB();	
			$check_query = "SELECT COUNT * from ratings WHERE video_id = ? AND user_id = ?";
			$check_params = [$video_id, $user_id];
			$rated_before = ($db->countRows($check_query, $check_params) < 0);
			
			if($rated_before){
				// ratings table
				$ratings_query = "UPDATE ratings SET rating = ?, time = ? WHERE video_id = ? AND user_id = ?";
				$ratings_params = [$new_rating, $time, $video_id, $user_id];
				$ratings_updated = $db->updateRow($ratings_query, $ratings_params);
				
				// videos table
				$new_average = $this->calculate_rating($new_rating, $video_id);
				$videos_query = "UPDATE videos SET video_rating = ?, time = ? WHERE video_id = ?";
				$videos_params = [$new_average, $video_id];
				$videos_updated = $db->updateRow($videos_query, $videos_params);
			}else{
				// ratings table
				$ratings_query = "INSERT INTO ratings (video_id, user_id, rating, time) VALUES (?,?,?,?)";
				$ratings_params = [$video_id, $user_id, $new_rating, $time];
				$ratings_updated = $db->insertRow($ratings_query, $ratings_params);
				
				// videos table
				$videos_query = "UPDATE videos SET video_rating = ?, time = ? WHERE video_id = ?";
				$videos_params = [$new_rating, $video_id];
				$videos_updated = $db->updateRow($videos_query, $videos_params);
			}
			
			if(!$ratings_updated OR !$videos_updated){
				$this->add_error(__METHOD__, "Eintrag der neuen Bewertung fehlgeschlagen");
				return false;
			}else{
				return true;
			}	
		}
	}

/* ===================================================================================== */
	
	/**
	 * Calculates a video's rating
	 * @param mixed $new_rating
	 * @return float 
	 */
	private function calculate_rating($new_rating, $video_id){
		
		$db = new DB();
		
		$query = "SELECT rating FROM ratings WHERE video_id = ?";
		$params =[$video_id];
		
		$stored_ratings = $db->getRows($query, $params);
		
		var_dump($stored_ratings);
		//$number = array
		
		$all_ratings = [$new_rating];
		
		foreach($stored_ratings as $rating){
			
			array_push($all_ratings, intval($rating['rating']));
		}
		$count = count($all_ratings);
		$sum = array_sum($all_ratings);
		$average = $sum/$count;
		$average = round($average, 0);
		
// 		return $average;
var_dump($average);
		
	}

	
/* ===================================================================================== */	
	
	/**
	 * Deletes a video (video file, thumbnail file and database entry)
	 * @param mixed $video_id 
	 * @param string $video_file File location
	 * @param string $thumbnail_file File location
	 * @param boolean $is_admin
	 * return boolean
	 */
	public function delete_video($video_id, $video_file, $thumbnail_file, $is_admin){
		
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{
				
			$db = new DB();
			
			//delete video file
			$delete_file = $this->delete_file($video_file, "video");
			
			//delete thumbnail
			$delete_thumbnail = $this->delete_file($thumbnail_file, "thumb");
			
			//delete db entry
			$delete_query = "DELETE FROM videos WHERE video_id = ?";
			$delete_params = [$video_id];
			$delete_entry = $db->deleteRow($delete_query, $delete_params);
			
			
			if(!$delete_file){
				$this->add_error(__METHOD__, "Löschen des Videos fehlgeschlagen");
				return false;
			}elseif(!$delete_thumbnail){
				$this->add_error(__METHOD__, "Löschen der Thumbnail-Datei fehlgeschlagen");
				return false;
			}elseif(!$delete_entry){
				$this->add_error(__METHOD__, "Löschen des Datenbankeintrags fehlgeschlagen");
				return false;
			}else{
				$this->add_message("Video erfolgreich gelöscht");
				return true;
			}
		}
	}
	
	
/* ===================================================================================== */
		
	/**
	 * Uploads a thumbnail image to the img directory
	 * @param array $new_file $_FILES array for the new thumbnail
	 * @param mixed $video_id
	 * @param boolean $is_admin
	 * @return boolean
	 */
	public function upload_thumbnail($new_file, $video_id, $is_admin){
	
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{		
			$file = $new_file['thumbnail'];
			
			$file_name = $file['name'];
			$file_name = $this->replace_umlauts($file_name);
			$file_name = htmlspecialchars($file_name);
			$file_tmp = $file['tmp_name'];
			$file_destination = THUMBS_DIR . $file_name;
			$thumb_upload = move_uploaded_file($file_tmp, $file_destination);
		
			if($thumb_upload){
		
				$valid_id = $this->valid_id($video_id);
	
				if($valid_id){
	
					$db = new DB();
					$query = "UPDATE videos SET thumbnail_file = ? WHERE video_id = ?";
					$params = [$file_name, $video_id];
						
					$db_upload = $db->updateRow($query, $params);
						
					return(array("video_id" => $video_id));
				}
				else{
					$this->add_error(__METHOD__, "Hochladen der Thumbnail-Datei fehlgeschlagen");
					return false;
				}
			}
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Deletes a video file or thumbnail image file
	 * @param string $file
	 * @param string $type 'Video' or 'thumb'
	 * @return boolean
	 */
	protected function delete_file($file, $type){
		
		switch($type){
			case "video":
				$dir = VIDEOS_DIR;
				$empty_msg = "Kein Datenbankeintrag für eine Video-Datei vorhanden";
				$nonexistant_msg = "Keine Videodatei zum Löschen vorhanden";
				break;
			case "thumb":
				$dir = THUMBS_DIR;
				$empty_msg = "Kein Datenbankeintrag für eine Thumbnail-Datei vorhanden";
				$nonexistant_msg = "Keine Thumbnail-Datei zum Löschen vorhanden";
				break;
		}
		
		$file_location = $dir . $file;
		$file_location = $this->replace_umlauts($file_location);
		$file_location = str_replace("\\", "/", $file_location);
		
		if($file == ""){	
			$this->add_message($empty_msg);
			return true;
		}elseif(!file_exists($file_location)){
			$this->add_message($nonexistant_msg);
			return true;
		}
		else{
			$delete_file = unlink($file_location);
			
			if(!$delete_file){
				$this->add_error(__METHOD__, "Löschen fehlgeschlagen: " . $file_location);
				return false;
			}else{
				$this->add_message("Löschen erfolgreich: " . $file_location);
				return true;
			}
		}		
	}
	
/* ===================================================================================== */

	/**
	 * Deletes a thumbnail file
	 * @param mixed $video_id
	 * @param boolean $is_admin
	 * @return boolean
	 */
	public function delete_thumbnail($video_id, $is_admin){
		
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{
				
		
			$db = new DB();
			$query = "SELECT thumbnail_file FROM videos WHERE video_id = ?";
			$params = [$video_id];
			
			$result = $db->getRow($query, $params);
			$thumbnail = $result['thumbnail_file'];
			
			if(!$thumbnail OR $thumbnail == ''){
				$this->add_message("Kein Datenbankeintrag für eine Bilddatei zum Löschen vorhanden");
				return true;
			}
			else if(!file_exists(THUMBS_DIR . $thumbnail)){
				$this->add_message("Keine Bilddatei zum Löschen vorhanden");
				return true;
			}
			else{
				//delete from database
				$delete_query = "UPDATE videos SET thumbnail_file = ? WHERE video_id = ?";
				$delete_params = ["", $video_id];
				$delete_from_db = $db->updateRow($delete_query, $delete_params);
				
				if(!$delete_from_db){
					$this->add_error(__METHOD__, "Löschen der Thumbnail-Referenz aus der Datenbank fehlseschlagen");
					return false;
				}
				else{
					//delete from directory
					$file_url = THUMBS_DIR . $thumbnail;
					$delete = unlink($file_url);
					
					if(!$delete){
						$this->add_error(__METHOD__, "Löschen der Thumbnail-Bilddatei fehlgeschlagen");
						return false;
					}
					else{
						return true;
					}	
				}
			}
		}
	}
		
/* ===================================================================================== */
	
	/**
	 * Changes thumbnail image for a video
	 * @param mixed $video_id
	 * @param array $file The edit form S_FILES entry
	 * @return boolean
	 */
	public function change_thumbnail($video_id, $file){
		
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{
				
			$delete_old = $this->delete_file($file, "");
			
			if(!$delete_old){
				$this->add_error(__METHOD__, "Löschen des alten Bildes fehlgeschlagen");
				return false;
			}else{
				
				$add_new = $this->upload_thumbnail($file, $video_id, true);
				
				if(!$add_new){
					$this->add_error(__METHOD__, "Hochladen des neuen Bildes fehlgeschlagen");
					return false;
				}else{	
					return true;
				}
			}
		}
	}
	
/* ===================================================================================== */

	/**
	 * Changes a video's public/private setting
	 * @param mixed $video_id
	 * @param mixed $video_public
	 * @param boolean $is_admin
	 * @return boolean
	 */
	public function set_public($video_id, $video_public, $is_admin){
		
		if(!$is_admin){
			$this->add_error(__METHOD__, "Administratorzugang benötigt");
			return false;
		}else{
		
			$video_public = intval($video_public);
			
			$db = new DB();
			$query = "UPDATE videos SET video_public = ? WHERE video_id = ?";
			$params = [$video_public, $video_id];
			$update = $db->updateRow($query, $params);
			
			if(!$update){
				$this->add_error(__METHOD__, "Eintrag fehlgeschlagen");
				return false;
			}else{
				$this->add_message("Video-Einstellungen geändert");
				return true;
			}
		}
	}
	

}// end of class	