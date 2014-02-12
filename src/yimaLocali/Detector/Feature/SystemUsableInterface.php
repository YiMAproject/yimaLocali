<?php
namespace yimaLocali\Detector\Feature;

interface SystemUsableInterface
{
	/**
	 * Be goonei khod raa dar system register mikonad ke ghaabele estefaade baashad
	 * masalan vaghti uriSegment [/fa] tashkhis daade mishavad,
	 * 
	 * dar Router e system baseUrl va dar Request Uri baayad taghir konad ke barnaame
	 * ghaabele ejraa baashad
	 * 
	 * vali masaln restrictedLocale be hich chiz ehtiaaj nadaarad.
	 */
    public function makeItUsable();
}
