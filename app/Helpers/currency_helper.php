<?php
	function currency_symbol()
	{
		return "$";
	}

	function format_price($price, $attach_currency_symbol=false)
	{
		$symbol = "";
		$formatted_price = number_format($price,2,".",",");
		if($attach_currency_symbol != true)
		{
			$symbol = currency_symbol();
			$formatted_price = $symbol.' '.$formatted_price;
		}
		return $formatted_price;
	}

	function currency_code()
	{
		return "AUD";
	}

?>