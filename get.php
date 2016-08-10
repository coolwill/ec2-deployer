<?php

	$rules = parse_ini_file('./rules.ini', true);

	if(!rules){
		header("HTTP/1.0 404 Config File Not Found", false, 404);
		exit;
	}

	foreach ($rules as $ruleName => $rule) {
		$matches = true;
		foreach ($rule as $tag => $value) {
			if($tag == "file") continue;

			if($value != $_GET[$tag]){
				$matches = false;
				break;
			}
		}
		if($matches){
			renderFile($rule["file"]);
			break;
		}
	}

	header("HTTP/1.0 404 Rule Not Found", false, 404);
	exit;
	

    function renderFile($filename){
    	if (file_exists($filename)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($filename));
			header('Content-Length: ' . filesize($filename));
    		readfile($filename);
    	}else{
    		header("HTTP/1.0 404 File Not Found", false, 404);
    	}
    	exit;
    }
?>
