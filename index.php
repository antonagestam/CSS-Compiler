<?php
	
	include_once "huffman.php";
	$h = new huffman();
	$h->input(array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','å','ä','ö',' '),array());
	$h->istring('jag är en väldigt duktig kille från jordanien');
	$h->calculate_weights();
	
	
	exit;
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