<?php
	class css_compiler
	{
		private $code;
		private $compiled;
		private $rules = array();
		private $selectors = array();
		private $compiled_code;
		
		public function __construct($code=false)
		{
			$this->compiled = false;
			$this->compiled_code = "";
			
			if($code !== false)
			{
				$this->init($code);
			}
		}
		
		public function init($code)
		{
			$this->code = $code;
			$this->compiled = false;
		}
		
		private function find_selectors()
		{
			// get a list of selectors
			preg_match_all('#([a-z|\#|\_|\.]+).*?{.*?}#si',$this->code,$matches);
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
		}
		
		public function compile()
		{
			$this->find_selectors();
			foreach($this->rules as $index => $rule)
			{
				foreach($this->selectors as $selector => $rules)
				{
					if(in_array($index,$rules))
					{
						$this->compiled_code .= $selector.',';
					}
				}
				$this->compiled_code = rtrim($this->compiled_code,',');
				$this->compiled_code .= '{'.$rule.'}';
			}
			$this->compiled = true;
		}
		
		public function retrieve()
		{
			if(!$this->compiled)
			{
				throw new Exception('Compiler has not been executed');
			}
			
			return $this->compiled_code;
		}
	}
	
	try
	{
		header('Content-type:text/css');
		$cc = new css_compiler();
		$cc->init(file_get_contents('style.css'));
		$cc->compile();
		echo $cc->retrieve();
	}
	catch(Exception $e)
	{
		echo $e->getMessage();
		exit;
	}