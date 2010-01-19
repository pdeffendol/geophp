<?php
namespace GeoPHP;

class EWKTTokenizer
{
	private $ewkt;
	private $pos;
	const REGEX = '/^\s*([\w.-]+)s*/';
	
	public function __construct($ewkt)
	{
		$this->ewkt = trim($ewkt); // Trim trailing whitespace
		$this->pos = 0;				
	}
	
	/**
	 * Get next token, and advance the position for scanning
	 */
	public function get_next_token()
	{
		$match = preg_match(self::REGEX, substr($this->ewkt, $this->pos), $matches);
		if ($match)
		{
			$this->pos += strlen($matches[0]);
			return $matches[1];
		}
		else
		{
			if ($this->eos())
			{
				return null;
			}
			else
			{
				$char = $this->ewkt[$this->pos++];
				while ($char == ' ')
				{
					$char = $this->ewkt[$this->pos++];
				}
				return $char;
			}
		}
	}
	
	
	/**
	 * Get next token, don't advance pointer
	 */
	public function check_next_token()
	{
		$match = preg_match(self::REGEX, substr($this->ewkt, $this->pos), $matches);
		if ($match)
		{
			return $matches[1];
		}
		else
		{
			if ($this->eos())
			{
				return null;
			}
			else
			{
				$pos = $this->pos;
				$char = $this->ewkt[$pos++];
				while ($char == ' ')
				{
					$char = $this->ewkt[$pos++];
				}
				return $char;
			}
		}
	}
	
	public function done()
	{
		if (!$this->eos())
		{
			throw new EWKTFormatError('Trailing data');
		}
	}
	
	private function eos()
	{
		return $this->pos == strlen($this->ewkt);
	}
}
?>
