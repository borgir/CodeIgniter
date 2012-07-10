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
 * @since		Version 2.1.0
 * @filesource
 */

/**
 * PDO 4D Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_4d_forge extends CI_DB_4d_forge {

	protected $_create_database	= 'CREATE SCHEMA %s';
	protected $_drop_database	= 'DROP SCHEMA %s';
	protected $_rename_table	= FALSE;

	/**
	 * Create Table
	 *
	 * @param	string	the table name
	 * @param	bool	should 'IF NOT EXISTS' be added to the SQL
	 * @return	string
	 */
	protected function _create_table($table, $if_not_exists)
	{
		$sql = 'CREATE TABLE ';

		if ($if_not_exists === TRUE)
		{
			$sql .= 'IF NOT EXISTS ';
		}

		return $sql
			.$this->db->escape_identifiers($table).' ('
			.$this->_process_fields()
			.$this->_process_primary_keys()
			."\n)";
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
		$sql = 'ALTER TABLE '.$this->db->escape_identifiers($table).' '.$alter_type.' '.$this->db->escape_identifiers($column_name);

		// DROP has everything it needs now.
		if ($alter_type === 'DROP')
		{
			return $sql;
		}

		return $sql.' '.$column_definition
			.($default_value !== '' ? ' DEFAULT "'.$default_value.'"' : '')
			.($null === NULL ? ' NULL' : ' NOT NULL')
			.($after_field !== '' ? ' AFTER '.$this->db->escape_identifiers($after_field) : '');

	}

}

/* End of file pdo_4d_forge.php */
/* Location: ./system/database/drivers/pdo/subdrivers/pdo_4d_forge.php */