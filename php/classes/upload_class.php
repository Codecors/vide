<?php

class Upload extends Video{
	
	/**
	 * upload class constructor
	 * @param array $post upload S_POST array
	 * @param array $file upload $_FILES array 
	 */
	public function __construct($post, $files){
		
		parent::__construct();
	
		$this->post = $post;
		$this->files = $files;
	}
	
	
/* ===================================================================================== */
	
	/**
	 * Extracts file type from a file name
	 * @param string $file_name
	 * @return string
	 */
	private function get_file_type($file_name){
		$type = explode('.', $file_name);
		$type = strtolower(end($type));
		return $type;
	}
	
/* ===================================================================================== */
	
	/**
	 * validates video title & description (upload form input)
	 * @param string $title
	 * @param string $desc
	 * @return array
	 */
	private function validate_upload($title, $desc){
		
		$valid_title = filter_var($title, FILTER_VALIDATE_REGEXP, array("options" => array(
				"regexp" => "/[a-zA-Zäüöß0-9\s\-\&]{1,50}/"
		)));
		
		$desc_length = strlen($desc);
		
		if($desc_length <= 300 ){
			$valid_desc = htmlspecialchars($desc, ENT_QUOTES);
		}else{
			$desc =  false;
		}
		
		if($valid_title && $desc){
			
			$valid_input = array(
					'video_title' => $valid_title,
					'video_desc' => $valid_desc
			);
			
			return $valid_input;
		}
	}
	
/* ===================================================================================== */
	
	/**
	 * Processes the $_FILES and $_POST arrays for upload
	 * @param array $post
	 * @param array $files
	 * @return array
	 */
	private function process_upload($post, $files){
		
		//validate form input
		$video_title = $post['title'];
		$video_desc = $post['desc'];
		
		$valid_input = $this->validate_upload($video_title, $video_desc);
		
		//get form input variables
		$video_title = $valid_input['video_title'];
		$video_desc = $valid_input['video_desc'];
		
		//get file input variables
		$video_file_name = $files['video']['name'];
		$video_file_name = $this->replace_umlauts($video_file_name);
		$video_file_type = $this->get_file_type($video_file_name);
		$video_file_tmp = $files['video']['tmp_name'];
		$video_file_path = VIDEOS_DIR . $video_file_name;
		$video_file_size = $files['video']['size'];
		$video_file_error = $files['video']['error'];
		
		//get the date
		$upload_date = date("d. m. Y");
		
		//combine variables to array for upload_video()
		$upload_array = array(
				'video_title'=> $video_title,
				'video_desc' => $video_desc,
				'video_file' => $video_file_name,
				'video_tmp_name' => $video_file_tmp,
				'video_file_type' => $video_file_type,
				'video_url' => $video_file_path,
				'video_file_size' => $video_file_size,
				'video_upload_error' => $video_file_error,
				'video_upload_date' => $upload_date
		);
		
		return($upload_array);
	}
	
/* ===================================================================================== */
		
	/**
	 * uploads video data & video file to db and directory
	 * @param boolean $is_admin
	 */
	public function upload_video($is_admin){
		
		if(!$is_admin){
			$this->add_error("Administratorzugang benötigt");
			return false;
		}else{
				
			$post = $this->post;
			$files= $this->files;
			
			$input = $this->process_upload($post, $files);
			
			//upload video file to directory
			
			$tmp = $input['video_tmp_name'];
			$destination = $input['video_url'];
		
			$video_upload = move_uploaded_file($tmp, $destination);
			
			if(!$video_upload){
				$this->add_error('Die Video-Datei konnte nicht hochgeladen werden');
				return false;
			}else{
					
				//upload data to db
				$db = new DB();
				
				$query = "INSERT INTO videos (video_title, video_desc, video_file, video_upload_date) VALUES (?,?,?,?)";
				
				$params = array(
						$input['video_title'],
						$input['video_desc'],
						$input['video_file'],
						$input['video_upload_date']
				);
				
				$data_upload = $db->addRow($query, $params);
				
				if(!$data_upload){
					$this->add_error('Die Daten konnten nicht zur Datenbank hinzugefügt werden');
					return false;
				}else{
					$new_row = $db->getRow("SELECT LAST_INSERT_ID()", []);
					return $new_row;
				}	
			}
		}
	}

		
}//end of class
	
