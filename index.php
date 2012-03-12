<?php
	class css_compiler
	{
		private $code;
		private $compiled = false;
		private $rules = array();
		private $selectors = array();
		private $compiled_code = "";
		private $singles = array();
		
		/**
		 * @param String $code
		 */
		public function __construct( $code = false )
		{
			if( $code !== false )
			{
				$this->init( $code );
			}
		}
		
		/**
		 * @param String $code - code to be compiled
		 */
		public function init($code)
		{
			if(empty($code))
			{
				throw new Exception("Must pass string in \$code");
			}
			$this->code = $code;
			$this->compiled = false;
		}
		
		private function find_selectors()
		{
			// get a list of all the selectors in the css code
			preg_match_all('#([a-z|\#|\_|\.|\-]+).*?{.*?}#si',$this->code,$matches);
			// loop the array and save the values in arrays
			foreach($matches[1] as $index => $selector)
			{
				$this->selectors[$selector] = array();
				$rules[$selector] = $matches[0][$index]; 
			}
			
			// loop through the rules (contents) of each selector
			foreach($rules as $index => $content)
			{
				// remove selector and opening bracket
				$content = preg_replace('#([a-z|\#|\_|\.]+).*?{#si','',$content);
				// remove closing bracket
				$content = str_replace('}','',$content);
				
				// find all rules TODO somewhere here support for aliasing (eg black goes #000000) should be added
				preg_match_all('#([a-z|\-]+?):([a-z|0-9|\#|\-|(|)|,|%| ]+);#si',$content,$matches);
				
				// loop through the rules
				foreach($matches[0] as $rule)
				{
					$this->rules[md5($rule)] = $rule;
					$this->selectors[$index][] = md5($rule);
				}
			}
			
			$this->clear_code();
		}
		
		/*
		 * Finds rules that only appear once
		 */
		private function is_single($rule_key)
		{
			$app = 0;
			foreach($this->selectors as $selector => $rules)
			{
				if(in_array($rule_key,$rules))
				{
					$app++;
				}
				
				if($app == 2)
				{
					break;
				}
			}
			
			if($app > 1)
			{
				return false;
			}
			else
			{
				$this->singles[] = $rule_key;
				return true;
			}
		}
		
		/*
		 * Clears the code
		 */
		public function clear_code()
		{
			$this->code = "";
		}
		
		public function compile()
		{
			// parse the css code into something more sofisticated; arrays
			$this->find_selectors();
			// inititate an array for rules that only occure once
			$singles = array();
			// loop through the rules
			foreach($this->rules as $index => $rule)
			{
				// find all singles
				if($this->is_single($index))
				{
					continue;
				}
				foreach($this->selectors as $selector => $rules)
				{
					if(in_array($index,$rules))
					{
						$this->compiled_code .= $selector.',';
					}
				}
				$this->compiled_code = rtrim($this->compiled_code,',');
				$this->compiled_code .= '{'.$rule.'}'."\n";
			}
			
			// loop through all the rules that only appear once and find out if it's selector has multiple singles and add them to the compiled code
			foreach($this->singles as $i => $rule_id)
			{
				
			}
			
			print_r($this->singles);
			print_r($this->selectors);
			print_r($this->rules);
			$this->compiled = true;
			
			return $this;
		}
		
		public function retrieve()
		{
			if(!$this->compiled)
			{
				throw new Exception('No code has been compiled');
			}
			
			return $this->compiled_code;
		}
	}
	
	try
	{
		header('Content-type:text/css');
		$cc = new css_compiler(file_get_contents('style.css'));
		echo $cc->compile()->retrieve();
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
		exit;
	}