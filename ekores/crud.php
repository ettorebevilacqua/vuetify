<?
	/*
	 * code from 
	 * https://github.com/mevdschee/php-crud-api/blob/master/api.php 
	 */

class Crud {	 
	
  
	public $method = null;
	public $request = null;
	public $get = null;
	public $post = null;
	public $origin = null;
	public $apiName='';
	private $apiObj= null;
	
  function startOutput() {
		if (isset($_SERVER['REQUEST_METHOD'])) {
			header('Content-Type: application/json; charset=utf-8');
		}
	}
	
 function allowOriginList($origin,$allowOrigins) {
		if (isset($_SERVER['REQUEST_METHOD'])) {
			header('Access-Control-Allow-Credentials: true');
			foreach (explode(',',$allowOrigins) as $o) {
				if (preg_match('/^'.str_replace('\*','.*',preg_quote(strtolower(trim($o)))).'$/',$origin)) { 
					header('Access-Control-Allow-Origin: '.$origin);
					break;
				}
			}
		}
	}
	
	 function allowOrigin() {

			// header('Access-Control-Allow-Credentials: true');
			// header('Access-Control-Allow-Origin: http://localhost:3000'); 
			 
	}
	
	function toArray(){ 
		$vals=[];
		$vals['method']=$this->method;
		$vals['request']=$this->request;
		$vals['get']=$this->get;
		$vals['post']=$this->post;
		$vals['origin']=$this->origin;
		return $vals;
	}

	
 function retrievePostData() {
		if ($_FILES) {
			$files = array();
			foreach ($_FILES as $name => $file) {
				foreach ($file as $key => $value) {
					switch ($key) {
						case 'tmp_name': $files[$name] = $value?base64_encode(file_get_contents($value)):''; break;
						default: $files[$name.'_'.$key] = $value;
					}
				}
			}
			return http_build_query(array_merge($files,$_POST));
		}
		return file_get_contents('php://input');
	}
	
	function callApi() {
		$function_name = $this->apiName;
		if(method_exists($this->apiObj, $function_name)){ 
			$this->apiObj->$function_name($this->toArray()); // passa i valori della request 
		} else  throw new Exception('crud error, method in apiObj not found');
	}
	
	function __construct($apiObj, $apiParamName="api"){
		
		$this->startOutput();
		$this->allowOrigin();
		
		$this->apiObj=$apiObj;
	
		$method = isset($method)?$method:null;
		$request = isset($request)?$request:null;
		$get = isset($get)?$get:null;
		$post = isset($post)?$post:null;
		$origin = isset($origin)?$origin:null;
		$apiParamName = isset($apiParamName)?$apiParamName:'api';
		$apiName = ''; 
		
		if (!$method) {
			$method = $_SERVER['REQUEST_METHOD'];
		}
		
		if (!$request) { 
			$request = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'';
			if (!$request) {
				$request = isset($_SERVER['ORIG_PATH_INFO'])?$_SERVER['ORIG_PATH_INFO']:'';
				$request = $request!=$_SERVER['SCRIPT_NAME']?$request:'';
			}
		}
		if (!$get) {
			$get = $_GET;
		}
		if (!$post) {
			$post = $this->retrievePostData();
		}
		if (!$origin) {
			$origin = isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'';
		}
		
		if (isset($_GET[$apiParamName])){
			$apiName=$_GET[$apiParamName];
		}
		
		$this->method=$method;
		$this->request=$request;
		$this->get=$get;
		$this->post=$post;
		$this->origin=$origin;
		$this->apiName=$apiName;
	}
}

?>

