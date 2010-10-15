<?php
	function weight($array,$val)
	{
		$w = 0;
		foreach($array as $value)
		{
			if($value == $val)
			{
				$w++;
			}
		}
		return $w;
	}
	
	function weights($alphabet,$symbols)
	{
		$weights = array();
		foreach($alphabet as $key => $val)
		{
			$weights[$key] = weight($symbols,$val);
		}
		return $weights;
	}
	
	class huffman
	{
		private $alphabet,$weights,$string;
		
		function input($a,$w)
		{
			$this->alphabet = $a;
			$this->weights = $w;
		}
		
		function istring($s)
		{
			$this->string = str_split($s);
		}
		
		function calculate_weights()
		{
			$this->weights = weights($this->alphabet,$this->string);
			echo '<table><thead>';
			echo '<tr><th>Symbol</th><th>Weight</th><th>Code</th></tr>';
			foreach($this->alphabet as $key => $symb)
			{
				$code = $this->weights[$key]/count($this->alphabet);
				echo "<tr><td>$symb</td><td>{$this->weights[$key]}</td><td>$code</td></tr>";
			}
		}
	}