<?php

class Master {

	protected $dbh;

	var $numberOfUsers = 0;

	var $timeoutSeconds = 900;//15mins till autodelete with refresh()

	/** logfile path / name
	 * @var String
	 */
	var $logfile = '';

	/** Error_text Array
	 * @var Array
	 */
	var $error_text = NULL;

	var $autodelete = false;

	var $HTTP_REFERER	= "";

	var $REQUEST_METHOD	= "";

	var $HTTP_HOST	= "";

	var $QUERY_STRING = "";

	var $SERVER_PROTOCOL = "";

	var $SCRIPT_NAME	= "";

	var $ip	= false;

	var $sess_id = false;

	var $timestamp = "";

	protected $db;

	public function setTimeStamp(){
				$this->timestamp=time();
		    return $this->timestamp;
    }
		public function setSess_id(){
		if (!session_id()){
			if(session_start()){$this->sess_id = session_id();}else{$this->sess_id =  'sessioncreaterror'; return $this->sess_id;}
		}
		else{
			$this->sess_id = session_id();
		}
    //    $this->sess_id = session_id();
		return $this->sess_id;
    }

    public function setSCRIPT_NAME(){
				$this->SCRIPT_NAME=$this->getit($_SERVER,'SCRIPT_NAME',254,'CLI');
				return $this->SCRIPT_NAME;
    }
    public function setSERVER_PROTOCOL(){
				$this->SERVER_PROTOCOL=$this->getit($_SERVER,'SERVER_PROTOCOL',254,'CLI');
				return $this->SERVER_PROTOCOL;
    }
    public function setQUERY_STRING(){
				$this->QUERY_STRING=$this->getit($_SERVER,'QUERY_STRING',254,'CLI');
				return $this->QUERY_STRING;
    }
    public function setHTTP_HOST(){
				$this->HTTP_HOST=$this->getit($_SERVER,'HTTP_HOST',254,'CLI');
				return $this->HTTP_HOST;
    }
    public function setREQUEST_METHOD(){
				$this->REQUEST_METHOD=$this->getit($_SERVER,'REQUEST_METHOD',254,'CLI');
				return $this->REQUEST_METHOD;
    }
		public function setHTTP_REFERER(){
				$tmp=getcwd();
				$this->HTTP_REFERER=$this->getit($_SERVER,'HTTP_REFERER',254,$tmp);
        return $this->HTTP_REFERER;
    }
    public function setIP(){
				$this->ip=$this->getIP();
    }
		public function setnumberOfUsers() {
				$timer=time()-$this->timeoutSeconds;

//$this->numberOfUsers = $this->dbh->query('select count(DISTINCT ip) from usersonline WHERE timestamp > '.$timer.' ')->fetchColumn();
$this->numberOfUsers = $this->dbh->query('select count(DISTINCT sess_id) from usersonline WHERE timestamp > '.$timer.' ')->fetchColumn();


				return $this->numberOfUsers;
    }
		public function getnumberOfUsers() {
				return $this->numberOfUsers;
    }
		public function printNumber() {
        if($this->numberOfUsers == 1) {
            return $this->numberOfUsers. " User";
        } else {
            return $this->numberOfUsers. " Users";
        }
    }



	/** Set ErrorText[]
	 * @param 	String		$text into error_text[] array
	 */
	public function setErrorText($text){
		$this->error_text[]=$text;
	}
	/** Get ErrorText[]
	 * @return 	Array	or NULL
	 */
	public function getErrorText(){
		return $this->error_text;
	}

	/** Set logfile path / name
	 * @param 	String		$logfile 		- logfile path / name
	 */
	public function setLogFilename($text){
		$this->logfile=$text;
		$temptext = PHP_EOL.
		$this->getdate().'-------------------------'.PHP_EOL.
		$this->getIP().'	Creating New Log entries'.PHP_EOL;
		$this->write_log($temptext);
		return $this->logfile;
	}
	/** Get logfile path / name
	 * @return 	String		$logfile 		- logfile path / name
	 */
	public function getLogFilename(){
		return $this->logfile;
	}
	/** Write text to logfile path / name
	 * @param 	String		$text to write append
	 */
	public function write_log($text){
		if(!file_exists($this->getLogFilename())){
			$this->setErrorText("logfile doesnt exist=".$this->logfile);
			@touch($this->logfile);
			}
		if(!is_writable($this->getLogFilename())){
			$this->setErrorText("Unable to create logfile=".$this->logfile);
			$this->setErrorText($text);
			return;
		}
		$temptext = '	'.$text.PHP_EOL;
		file_put_contents($this->logfile, $temptext, FILE_APPEND);
	}
	/** Display logfile path / name / contents
	 * @return 	String		$logfile 		- logfile path / name
	 */
	public function getLogFileContents(){
		return readfile($this->logfile);
	}
	/**
	 * Will return the current date & time.
	 * @param 	String timestamp
	 * @return 	String formatted Date & Time
	 **/
	public function getdate($time=false) {
		if(!$time){$time=time();}
		return date('F j, Y', $time).' at '.date('h:ia', $time);
	}





	public function valid_ip($ip){
			$ip_segments = explode('.', $ip);
			// Always 4 segments needed
			if (count($ip_segments) != 4){return "1.0.0.0";}
			// IP can not start with 0
			if ($ip_segments[0][0] == '0'){return "2.0.0.0";}
			// Check each segment
			foreach ($ip_segments as $segment){
				// IP segments must be digits and can not be
				// longer than 3 digits or greater then 255
				if ($segment == '' OR preg_match("/[^0-9]/", $segment) OR $segment > 255 OR strlen($segment) > 3)
				{return "3.0.0.0";}
			}
	return $ip;
	}

	public function getIP(){
		if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            return  $this->valid_ip($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $this->valid_ip($_SERVER["REMOTE_ADDR"]);
    }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $this->valid_ip($_SERVER["HTTP_CLIENT_IP"]);
    }
    return "0.0.0.0";
  }


	public function file_write_contents($file_name,$contents=false)
	{
	  if($contents){
	    $myfile = fopen($file_name, 'w') or die('Cannot open file: '.$file_name);
	    fwrite($myfile, $contents);
	    fclose($myfile);
	    }
	}

	public function getit($Requested_Array=array(),$key='',$length=254,$defaut=false){
    //Get or Post or Request or cookie, any Array, etc
    $temp="";
    $temp=(isset($Requested_Array[$key])) ? trim(strip_tags($Requested_Array[$key])) : $defaut;
    $temp=htmlspecialchars($temp, ENT_QUOTES, 'UTF-8');
    $temp=substr($temp, 0, $length);
    return $temp;
  }

	public function getit_raw($Requested_Array=array(),$key=''){
    //Get or Post or Request or cookie, any Array, etc
    $temp="";
    $temp=(isset($Requested_Array[$key])) ? trim($Requested_Array[$key]) : false;
    return $temp;
  }

	public function flipflop($a=true,$b=false,$var=false)
  {
    if($var==$b){return $a;}
    elseif($var==$a){return $b;}
    else{return $var;}
  }



	public function inspect($str) {
  if($str==""){return false;}

	$find = array(
			"/[\r\n]/",
			"/%0[A-B]/",
			"/%0[a-b]/",
			"/bcc\:/i",
			"/Content\-Type\:/i",
			"/Mime\-Version\:/i",
			"/cc\:/i",
			"/from\:/i",
			"/to\:/i",
			"/Content\-Transfer\-Encoding\:/i"
	);
	$ret = preg_replace($find, "", $str);
	return $ret;
  }



	public function encrypt_decrypt($action, $string) {
	    $output = false;

	    $encrypt_method = "AES-256-CBC";
	    $secret_key = 'iD3m2sX9GYpu';
	    $secret_iv = 'rN6KuibF8KGQ';

	    // hash
	    $key = hash('sha256', $secret_key);

	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);

	    if( $action == 'encrypt' ) {
	        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	        $output = base64_encode($output);
	    }
	    else if( $action == 'decrypt' ){
	        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    }

	    return $output;
	}



	public function RandomToken($length = 32){
	    if(!isset($length) || intval($length) <= 8 ){
	      $length = 32;
	    }
	    if (function_exists('random_bytes')) {
	        return bin2hex(random_bytes($length));
	    }
	    if (function_exists('mcrypt_create_iv')) {
	        return bin2hex(mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
	    }
	    if (function_exists('openssl_random_pseudo_bytes')) {
	        return bin2hex(openssl_random_pseudo_bytes($length));
	    }
	}

	public function Salt(){
	    return substr(strtr(base64_encode(hex2bin($this->RandomToken(32))), '+', '.'), 0, 44);
	}


	public function funcCheckEmail($sEmailAddress)
  {
      // Regex of valid characters
      $sChars = "^[A-Za-z0-9\._-]+@([A-Za-z][A-Za-z0-9-]{1,62})(\.[A-Za-z][A-Za-z0-9-]{1,62})+$";
      // Check to make sure it is valid
      $bIsValid = true;
      if(!ereg("$sChars",$sEmailAddress))
      {
      $bIsValid = false;
      }
      return $bIsValid;
  }

	public function redirect($url)
  {
  		header("Location: $url");
  }





	public function __construct(){
		$this->setLogFilename($this->logfile);
		$this->dbh();
		$this->init();
	}

	public function init()
	{
		$this->setSess_id();
		$this->setTimeStamp();
		$this->setIP();
		$this->setSCRIPT_NAME();
		$this->setQUERY_STRING();
		$this->setHTTP_HOST();
		$this->setHTTP_REFERER();
		$this->setREQUEST_METHOD();
		$this->setSERVER_PROTOCOL();
		$this->addDB();
		$this->setnumberOfUsers();

			if (isset($_GET['logout'])) {
					//$this->logout();
			}
	}

	public function dbh()
    {
		try {
		     $this->dbh = new PDO("mysql:host=localhost;dbname=klik_loginsystem", "root", "1234");
		     $this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		     # $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
		     # $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  		   //$this->write_log('Successfully dbhed to the database!');
			 }
		catch(PDOException $e) {
				 $this->write_log($e->errorInfo());
				 $this->setErrorText( $e->errorInfo());
		}
		return $this->dbh;
	}

	protected function query($stm, $values = array())
	 {
			 $query = $this->dbh->prepare($stm);
			 $query->execute($values);
			 //var_dump($query);
			 return $query;
	 }



	 ///////////////////////////////////////////////////////////////////////////
	 /*	$stmt->rowCount()
	 -1 - Query returned an error. Redundant if there is already error handling for execute()
	  0 - No records updated on UPDATE, no rows matched the WHERE clause or no query been executed; just rows matched if PDO::MYSQL_ATTR_FOUND_ROWS => true
	  1 - Greater than 0 - Returns number of rows affected;

		$table='';
		$index_field='';
		$index_value='';
		$field_to_update='';
		$data_to_update='';
		$err=$ol->update_fieldPDO($table,$index_field,$index_value,$field_to_update,$data_to_update);


	 */
	 ///////////////////////////////////////////////////////////////////////////
public function update_fieldPDO($table,$index_field,$index_value,$field_to_update,$data_to_update){
		 $table=trim($table);$index_field=trim($index_field);$index_value=trim($index_value);$field_to_update=trim($field_to_update);$data_to_update=trim($data_to_update);
	   $sql = "UPDATE $table SET $field_to_update=? WHERE ".$index_field."=?";
	   $stmt= $this->dbh->prepare($sql);
		 if($stmt==false){return false;}
	   $stmt->execute([$data_to_update, $index_value]);
	   return $stmt->rowCount();
	 }
	 ///////////////////////////////////////////////////////////////////////////
/*
$table='';
$index_field='';
$index_value='';
$field_to_update='';
$data_to_update='';
$err=$ol->update_fieldPDO($table,$index_field,$index_value,$field_to_update,$data_to_update);

*/
	 ///////////////////////////////////////////////////////////////////////////
public function get_fieldPDO($table,$search_field,$search_value,$return_field){
		 $table=trim($table);$search_field=trim($search_field);$search_text=trim($search_text);$return_field=trim($return_field);
		 $query='SELECT * FROM '.$table.' WHERE `'.$search_field.'` = :'.$search_field;
	   $stmt = $this->dbh->prepare($query);
	   $stmt->bindParam($search_field, $search_value , PDO::PARAM_STR, 64);
	   $stmt->execute();
	   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	     //$stmt = null;
	      return $row[$return_field];
	    }//end while
	 return false;
	 }
	 ///////////////////////////////////////////////////////////////////////////
	 /*
	 false if nothing found
	 array() returns assoc array

	 $table='';
	 $search_field'';
	 $search_text='';
	 $err=$ol->get_arrayPDO($table,$search_field,$search_text);

	 */
	 ///////////////////////////////////////////////////////////////////////////
public function get_arrayPDO($table,$search_field,$search_text){
		 $table=trim($table);$search_field=trim($search_field);$search_text=trim($search_text);
	   $query='SELECT * FROM '.$table.' WHERE `'.$search_field.'` LIKE :'.$search_field;
	   //echo '  '.$query;
	   $stmt = $this->dbh->prepare($query);
	   $stmt->bindParam($search_field, $search_text , PDO::PARAM_STR, 64);
	   $stmt->execute();
	   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	     //$stmt = null;
	      return $row;
	    }//end while
	 return false;
	 }
	 ///////////////////////////////////////////////////////////////////////////
	 /*
	 false if nothing found
	 array() returns assoc array

	 $table='';
	 $search_field'';
	 $search_text='';
	 $err=$ol->delete_itPDO($table,$search_field,$search_text);

	 */
	 ///////////////////////////////////////////////////////////////////////////
public function delete_itPDO($table,$search_field,$search_text,$q=""){
	$table=trim($table);$search_field=trim($search_field);$search_text=trim($search_text);
	$query='DELETE FROM '.$table.' WHERE `'.$search_field.'` = :'.$search_field .' '. $q;
	$stmt = $this->dbh->prepare($query);
	$stmt->bindParam($search_field, $search_text, PDO::PARAM_STR);
	$executed = $stmt->execute();
	if($executed){return true;}else{return false;}

}








	 public function addDB()
     {
					$stmt = $this->dbh->prepare("insert into usersonline (`ip`, `sess_id`, `QUERY_STRING`, `HTTP_HOST`, `HTTP_REFERER`, `REQUEST_METHOD`, `timestamp` ) values (:ip, :sess_id, :QUERY_STRING, :HTTP_HOST, :HTTP_REFERER, :REQUEST_METHOD, :timestamp )");
					   //$stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR, 64);
					   $stmt->bindParam(':ip', $this->ip, PDO::PARAM_STR, 64);
					   $stmt->bindParam(':sess_id', $this->sess_id, PDO::PARAM_STR, 64);
					   $stmt->bindParam(':QUERY_STRING', $this->QUERY_STRING, PDO::PARAM_STR, 64);
					   $stmt->bindParam(':HTTP_HOST', $this->HTTP_HOST, PDO::PARAM_STR, 64);
					   $stmt->bindParam(':HTTP_REFERER', $this->HTTP_REFERER, PDO::PARAM_STR, 64);
					   $stmt->bindParam(':REQUEST_METHOD', $this->REQUEST_METHOD, PDO::PARAM_STR, 64);
					   $stmt->bindParam(':timestamp', $this->timestamp, PDO::PARAM_STR, 64);
					$executed = $stmt->execute();
					if($executed){
					   //$this->write_log('Successfully updated the database!');
					}else{
						$this->write_log($stmt->errorInfo());
							$this->setErrorText( $stmt->errorInfo());
					}

			}
 		//echo $db_message.'<BR>';
		public function displat_useronline(){
				$timer=time()-$this->timeoutSeconds;
				$stmt = $this->dbh->prepare('SELECT * FROM usersonline WHERE `timestamp` > '.$timer.' ORDER BY timestamp DESC LIMIT 25');
				$stmt->bindParam('timestamp', $search_for , PDO::PARAM_STR, 64);
				$stmt->execute();
				echo '<table class="table table-striped table-hover ">';
				echo '<thead><tr>';
				echo "<th>id</th>";
				echo "<th>ip</th>";
				echo "<th>sess_id</th>";
				echo "<th>query</th>";
				echo "<th>HTTP_HOST</th>";

				echo "<th>HTTP_REFERER</th>";
				echo "<th>REQUEST_METHOD</th>";
				echo "<th>timestamp</th>";
				echo '</tr></thead><tbody>';
     		while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
					echo "<tr>";
          echo "<td>".$row['id']."</td>";
          echo "<td>".$row['ip']."</td>";
          echo "<td>".$row['sess_id']."</td>";
          echo "<td>".$row['QUERY_STRING']."</td>";
          echo "<td>".$row['HTTP_HOST']."</td>";
          echo "<td>".$row['HTTP_REFERER']."</td>";
          echo "<td>".$row['REQUEST_METHOD']."</td>";
          echo "<td>".$row['timestamp']."</td>";
					echo "</tr>";
     			}//end while
				echo '</tbody></table>';

     		$stmt = null;
 	}



	###############################################
	# Display images in Current_Directory
	###############################################
	public function gallery($directory = 'images/gallery', $display_type = 'thumbnails'){
$files=false;
echo "<div class='slideshow'>";
/* step one:   */
$html_dir = str_replace($_SERVER['DOCUMENT_ROOT'], "", getcwd());
$html_dir = 'https://' . $_SERVER['HTTP_HOST'] . $html_dir;
$pattern="/(\.jpg$)|(\.tfif$)|(\.png$)|(\.jpeg$)|(\.gif$)/i"; //valid image extensions
//echo '<br>html_dir='.$html_dir;
//echo '<br>html_dir2='.$html_dir2;
/* step two:  read directory, make array of images */
if ($handle = opendir($directory)) {
	while (false !== ($file = readdir($handle)))
		{
		if(preg_match($pattern, $file)){ //if this file is a valid image
			$files[] = $file;
			}
		}
	closedir($handle);
}
if(!$files){return;}
/* step two: loop through, format gallery */
if(count($files)){
	sort($files);
	foreach($files as $file){
		//$start = 0;
		//$end = strpos($file, '.jpg');
		$caption = $file;
		//$caption = substr($file, $start, $end);
		$caption = str_replace('-',' ',$caption);
		$caption = ucfirst(str_replace('_',' ',$caption));
		switch($display_type){
			case 'all-the-way':
				echo "<div class='slide'><img src='$html_dir/$file' alt='$caption' /><h2 class='caption'>$caption</h2><div class='pager'></div></div>";
				break;
			case 'thumbnails':
				echo "<a href='$html_dir/$file' target='_blank'><img src='$html_dir/$file'  width='300' height='225' alt='$caption' /></a>";
				break;
			case 'slideshow':
				echo "<div class='slide'><img src='$html_dir/$file' alt='$caption' /></div>";
				break;
			case 'captioned-slideshow':
				echo "<div class='slide'><img src='$html_dir/$file' alt='$caption' /><h2 class='caption'>$caption</h2></div>";
				break;
			case 'list':
				echo "<li><img src='$html_dir/$file' alt='$caption' /></li>";
				break;
		}
	}
}else{
	echo '<p>There are no images in this gallery.</p>';
}
echo "</div>";
}



}
