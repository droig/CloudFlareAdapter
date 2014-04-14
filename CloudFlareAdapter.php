<?php
/**
 * CloudFlare API PHP Framework
 * 
 * By now, this can only edit a record but you can extend it for your needs.
 * 
 * USE:
 * 
 * $adapter = new CloudFlareAdapter('44cbd278d1333059b874ad54741e782b', 'foo@bar.com');
 * if ( $adapter->editRecord('bar.com', 'A', 'foo.bar.com', '1.2.3.4') )
 * 		//success actions
 * else
 * 		//error actions
 * 
 */


class CloudFlareAdapter {

	const CF_URL = 'https://www.cloudflare.com/api_json.html';

	var $tkn;
	var $email;
	
	private $rec;
	private $recs;
	
	private $error;
	private $result;
	
	private $baseVars = array();
		
	function __construct($tkn, $email) {
		$this->email = $email;
		$this->tkn = $tkn;
		
		$this->baseVars = array(
			'tkn' => $this->tkn,
			'email' => $this->email,
		);
	}
	
	
	/**
	 * 
	 * 
	 */
	public function editRecord($domain, $type, $name, $value) {
		
		try {
			
			$this->recs = $this->getRecs($domain);
		
			$recToUpdate = $this->getRec($name, $type, $this->recs);
	
			$editData = array(
				'a' => 'rec_edit',
				'z' => $domain,
				'type' => $recToUpdate->type,
				'name' => $recToUpdate->name,
				'id' => $recToUpdate->rec_id,
				'content' => $value,
				'service_mode' => $recToUpdate->service_mode,
				'ttl' => $recToUpdate->ttl,
				'tkn' => $this->tkn,
				'email' => $this->email
			);
	
			
			$result = $this->request($editData);
			$json = json_decode($result);
			
			//echo $result;
			return $this->successResponse($json);
			
			
			
			
		} catch (Exception $e) {
			$this->error = $e->getMessage();
			return false;
		} 
	
	}
	
	
	
	
	/**
	 * 
	 * 
	 */
	private function getRecs($domain) {
		
		$post = array(
		        'a' => 'rec_load_all',
		        'tkn' => $this->tkn,
		        'email' => $this->email,
		        'z' => $domain
		);
		
		$response = $this->request($post);
		$json = json_decode ( $response );
		
		if ($json == NULL) 
			throw new Exception("Bad JSON: Maybe bad credentials? \n$response");
		$this->successResponse($json);

		return $json->response->recs->objs;
	}
	
	
	
	
	
	
	/**
	 * Check server response and throw exception if error
	 * 
	 * {"result":"error","msg":"Unknown token or email","err_code":"E_UNAUTH"}
	 * {"request":{"act":"rec_edit"},"result":"error","msg":"Invalid record id."}
	 * 
	 */
	private function successResponse($json) {
		if ($json->result && $json->result == 'success') {
			return true;
		}
		else if ($json->result && $json->result == 'error') {
			throw new Exception("Server error: \n" . $json->msg);
		}
	}
	
	
	
	
	
	/**
	 * 
	 * 
	 * 
	 */
	private function request($post_data) {
	
			$postdata = http_build_query($post_data);
	
			$opts = array(
			  'http' => array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			  )
			);
			$context  = stream_context_create($opts);
			$result = @file_get_contents( self::CF_URL , false, $context);
			
			if ($result === false) throw new Exception("HTTP Request failed. Check connection and verify OpenSSL extension is activated.");
			
			return $result;
	}




	/**
	 * Get a record ID by name and type
	 * Example: getRecId('foo.com', 'MX');
	 */
	private function getRec($name, $type, $recs) {

		foreach ($recs as $rec) {
			if ($rec->type == $type && $rec->name == $name) {
				return $rec;
			}
		}
		throw new Exception("Record not found: Name: $name, Type: $type");
	}
	
	
	
	/**
	 * Get error message
	 */
	function getError($echo = false) {
		if ($echo)
			echo $this->error;
		else
			return $this->error;
	}

}
