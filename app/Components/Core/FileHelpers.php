<?php

namespace App\Components\Core;

Trait FileHelpers
{
    /**
     *  helper to get filetype
     *
     * @param $mimetype
     * @param string|file $default
     * @return string
     */
    public static function getFileTypeHelper($mimetype,$default = 'file')
    {

    	if(strpos($mimetype, 'pdf') !== false)
    	{
    		return 'pdf';
    	}
    	elseif (strpos($mimetype, 'csv') !== false || strpos($mimetype, 'excel') !== false || strpos($mimetype, 'spreadsheetml') !== false)
    	{
    		return 'sheet';
    	}
    	elseif (strpos($mimetype, 'powerpoint') !== false || strpos($mimetype, 'presentationml') !== false || strpos($mimetype, 'opendocument') !== false || strpos($mimetype, 'msword') !== false || strpos($mimetype, 'wordprocessingml') !== false)
    	{
    		return 'docs';
    	}
    	elseif (strpos($mimetype, 'image') !== false)
    	{
    		return 'image';
    	}
    	elseif (strpos($mimetype, 'audio') !== false)
    	{
    		return 'audio';
    	}
    	elseif (strpos($mimetype, 'video') !== false)
    	{
    		return 'video';
    	}
    	elseif (strpos($mimetype, 'text') !== false || strpos($mimetype, 'json') !== false || strpos($mimetype, 'php') !== false || strpos($mimetype, 'typescript') !== false)
    	{
    		return 'text';
    	}
        else
        {
    		return $default;
    	}

    }

}