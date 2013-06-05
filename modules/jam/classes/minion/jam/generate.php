<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Generate a model file
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Minion_Jam_Generate {

	public static function modify_file($file, $content, $force = FALSE, $unlink = FALSE)
	{
		if ($unlink)
		{
			$file_already_exists = is_file($file);
			
			if ($file_already_exists)
			{
				unlink($file);
				Minion_CLI::write("Removed file ".Debug::path($file), 'light_green');		
			}
			else
			{
				Minion_CLI::write("File does not exist ".Debug::path($file), 'brown');	
			}
		}
		elseif ($force)
		{
			$file_already_exists = is_file($file);
			file_put_contents($file, $content);
			if ($file_already_exists)
			{
				Minion_CLI::write("Overwritten file ".Debug::path($file), 'brown');		
			}
			else
			{
				Minion_CLI::write("Generated file ".Debug::path($file), 'light_green');	
			}
		}
		else
		{
			if (is_file($file))
			{
				Minion_CLI::write("File already exists ".Debug::path($file), 'brown');
			}
			else
			{
				file_put_contents($file, $content);
				Minion_CLI::write("Generated file ".Debug::path($file), 'light_green');	
			}
		}
	}
}