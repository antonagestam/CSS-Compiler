<?php
	/*
	 * @author Anton Agestam
	 * @copyright 2010
	 * @project css_compiler
	 * @version 1.0.0
	 */
	class css_compiler
	{
		private $uncompr_len = 0;
		private $compr_len = 0;
		private $code;
		private $compiled;
		private $rules = array();
		private $selectors = array();
		private $compiled_code;
		private $parsed;
		
		public function __construct($code=false)
		{
			$this->compiled = $this->parsed = false;
			$this->compiled_code = "";
			
			if($code !== false)
			{
				$this->init($code);
			}
		}
		
		public function init($code)
		{
			if(empty($code))
			{
				throw new Exception("Must pass string in \$code");
			}
			$this->clear_code();
			$this->code = $code;
			$this->uncompr_len = strlen($this->code);
			$this->compiled = false;
		}
		
		/*
		 * Puts all the selectors and rules apart
		 */
		private function parse_code()
		{
			$patterns = array(
				'get_selectors' => '#([a-z|\#|\_|\.|0-9|,]+).*?{.*?}#si',
				'get_rules' => '#([a-z|\-]+?):([a-z|0-9|\#|\-|(|)|,|%| ]+);#si',
				'remove_selector' => '#([a-z|\#|\_|\.]+).*?{#si',
			);
			
			// get all selectors
			preg_match_all($patterns['get_selectors'],$this->code,$selectors);
			
			// turn css code into arrays
			foreach($selectors[0] as $i => $code)
			{
				// remove some code
				$selectors[0][$i] = preg_replace($patterns['remove_selector'],'',$code);
				$selectors[0][$i] = str_replace('}','',$selectors[0][$i]);
				
				// turn code into arrays again
				preg_match_all($patterns['get_rules'],$selectors[0][$i],$rules);
				$selectors[0][$i] = $rules[0];
			}
			
			foreach($selectors[0] as $id => $selector)
			{
				foreach($selector as $rule)
				{
					if(!isset($this->rules[$rule]))
					{
						$this->rules[$rule] = array();
					}
					
					$this->rules[$rule][] = $selectors[1][$id];
				}
			}
			
			$this->parsed = true;
			$this->clear_code();
		}
		
		/*
		 * Makes cool stuff
		 */
		private function combine()
		{
			// implode selectors that share rules
			foreach($this->rules as $rule => $selectors)
			{
				$sel_name = implode($selectors,',');
				$this->rules[$rule] = $sel_name;
			}
			
			// flip array and combine singles
			foreach($this->rules as $rule => $selector)
			{
				$this->selectors[$selector][] = $rule;
			}
		}
		
		/*
		 * Clears the code
		 */
		public function clear_code()
		{
			$this->code = "";
		}
		
		/*
		 * Glues all the selectors and rules back together again
		 */
		public function compile()
		{
			// parse the css code into something more sofisticated; arrays
			$this->parse_code();
			$this->combine();
			
			foreach($this->selectors as $selector => $rules)
			{
				$this->compiled_code .= $selector."{".implode($rules)."}";
			}
			
			$this->compiled = true;
			$this->compr_len = strlen($this->compiled_code);
			return $this;
		}
		
		/*
		 * Returns your compiled code
		 */
		public function retrieve()
		{
			if(!$this->compiled)
			{
				throw new Exception('Compiler has not been executed');
			}
			
			return $this->compiled_code;
		}
		
		public function benchmark()
		{
			return "Turned $this->uncompr_len characters of code into $this->compr_len";
		}
	}
	
	try
	{
		header('Content-type:text/css');
		$cc = new css_compiler(file_get_contents('style.css'));
		echo $cc->compile()->retrieve();
		echo "/*",$cc->benchmark(),"*/";
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
		exit;
	}