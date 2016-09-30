<?php 
/**
 * parent class that sets up message handling system and functions used by several classes
 */
class Main{
	
	/**
	 * arrays for storing message
	 */
	public $errors;
	public $general_messages;
	public $all_messages;
	
	/**
	 * bootstrap classes to be assigned to message types
	 */
	const ERR_MSG_CLASS = "alert alert-danger";
	const GEN_MSG_CLASS = "alert alert-success";
	
	/**
	 * sets up empty arrays for messages, error warnings and both combined
	 */
	public function __construct(){
		$this->errors = [];
		$this->general_messages = [];
		$this->all_messages = [];
	}
	
/* ===================================================================================== */
	
	/**
	 * adds error message to errors and all_messages
	 * @param string $msg
	 */
	public function add_error($msg){
		$this->errors[] = $msg;
		
		$msg_array = array(
				'type' => 'error',
				'class' => $this::ERR_MSG_CLASS,
				'text' =>  $msg
		);
		
		$this->all_messages[] = $msg_array;
	}
	
/* ===================================================================================== */

	/**
	 * adds general message to general_messages and all_messages
	 * @param string $msg
	 */
	public function add_message($msg){
		$this->general_messages[] = $msg;
				
		$msg_array = array(
				'type' => 'general', 
				'class' => $this::GEN_MSG_CLASS,
				'text' =>  $msg
		);
		
		$this->all_messages[] = $msg_array;
	}

/* ===================================================================================== */
	
	/**
	 * replaces German special characters in strings
	 * @param string $input
	 * @return string
	 */
	public function replace_umlauts($input){
		$input = preg_replace( '@\x{00c4}@u' , "Ae", $input );
		$input = preg_replace( '@\x{00d6}@u' , "Oe", $input );
		$input = preg_replace( '@\x{00dc}@u' , "Ue", $input );
		$input = preg_replace( '@\x{00e4}@u' , "ae", $input );
		$input = preg_replace( '@\x{00f6}@u' , "oe", $input );
		$input = preg_replace( '@\x{00fc}@u' , "ue", $input );
		$input = preg_replace( '@\x{00df}@u' , "ss", $input );
		
		return $input;
	}
	
}