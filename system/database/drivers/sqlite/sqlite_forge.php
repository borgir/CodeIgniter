<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.2.4 or newer
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt / license.rst.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world wide web, please send an email to
 * licensing@ellislab.com so we can send you a copy immediately.
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2012, EllisLab, Inc. (http://ellislab.com/)
 * @license		http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

/**
 * SQLite Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite_forge extends CI_DB_forge {

	protected $_create_table_if	= FALSE;

	/**
	 * Create database
	 *
	 * @param	string	the database name
	 * @return	bool
	 */
	public function create_database($db_name = '')
	{
		// In SQLite, a database is created when you connect to the database.
		// We'll return TRUE so that an error isn't generated
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Drop database
	 *
	 * @param	string	the database name (ignored)
	 * @return	bool
	 */
	public function drop_database($db_name = '')
	{
		if ( ! @file_exists($this->db->database) OR ! @unlink($this->db->database))
		{
			return ($this->db->db_debug) ? $this->db->display_error('db_unable_to_drop') : FALSE;
		}
		elseif ( ! empty($this->db->data_cache['db_names']))
		{
			$key = array_search(strtolower($this->db->database), array_map('strtolower', $this->db->data_cache['db_names']), TRUE);
			if ($key !== FALSE)
			{
				unset($this->db->data_cache['db_names'][$key]);
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Alter table query
	 *
	 * Generates a platform-specific query so that a table can be altered
	 * Called by add_column(), drop_column(), and column_alter(),
	 *
	 * @param	string	the ALTER type (ADD, DROP, CHANGE)
	 * @param	string	the column name
	 * @param	string	the table name
	 * @param	string	the column definition
	 * @param	string	the default value
	 * @param	bool	should 'NOT NULL' be added
	 * @param	string	the field after which we should add the new field
	 * @return	string
	 */
	protected function _alter_table($alter_type, $table, $column_name, $column_definition = '', $default_value = '', $null = '', $after_field = '')
	{
		/* SQLite only supports adding new columns and it does
		 * NOT support the AFTER statement. Each new column will
		 * be added as the last one in the table.
		 */
		if ($alter_type !== 'ADD COLUMN')
		{
			// Not supported
			return FALSE;
		}

		return 'ALTER TABLE '.$this->db->escape_identifiers($table).' '.$alter_type.' '.$this->db->escape_identifiers($column_name)
			.' '.$column_definition
			.($default_value != '' ? " DEFAULT '".$default_value."'" : '')
			// If NOT NULL is specified, the field must have a DEFAULT value other than NULL
			.(($null !== NULL && $default_value !== 'NULL') ? ' NOT NULL' : ' NULL');
	}

	// --------------------------------------------------------------------

	/**
	 * Process fields
	 *
	 * @return	string
	 */
	protected function _process_fields()
	{
		foreach ($this->fields as $field => $attributes)
		{
			$attrs = array_change_key_case($attributes, CASE_UPPER);

			if (empty($attributes['TYPE']))
			{
				unset($this->fields[$field]);
				continue;
			}

			if (empty($attributes['NAME']))
			{
				$attributes['NAME'] = $field;
			}

			$this->fields[$field] = "\n\t".$this->db->escape_identifiers($attributes['NAME']);

			if ( ! empty($attributes['AUTO_INCREMENT']) && $attributes['AUTO_INCREMENT'] === TRUE && stripos($attributes['TYPE'], 'int') !== FALSE)
			{
				$attributes['TYPE'] = 'INTEGER PRIMARY KEY AUTOINCREMENT';
				in_array($attributes['NAME'], $this->primary_keys, TRUE) OR $this->primary_keys[] = $attributs['NAME'];
			}

			$this->fields[$field] .= ' '.$attributes['TYPE'];

			if (isset($attributes['DEFAULT']))
			{
				$this->fields[$field] .= ' DEFAULT '.$this->db->escape($attributes['DEFAULT']);
			}

			$this->fields[$field] .= (empty($attributes['NULL']) && $attributes['NULL'] === TRUE)
						? ' NULL' : ' NOT NULL';

			if ( ! empty($attributes['UNIQUE']) && $attributes['UNIQUE'] === TRUE)
			{
				$this->fields[$field] .= ' UNIQUE';
			}
		}

		if (empty($this->fields))
		{
			return FALSE;
		}

		return implode(',', $this->fields);
	}

}

/* End of file sqlite_forge.php */
/* Location: ./system/database/drivers/sqlite/sqlite_forge.php */