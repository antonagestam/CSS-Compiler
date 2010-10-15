<?php

	try
	{
		header('Content-type:text/css');
		include_once "css_compiler.php";
		$cc = new css_compiler(file_get_contents('style.css'));
		echo $cc->compile()->retrieve();
		echo "/*",$cc->benchmark(),"*/";
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
		exit;
	}