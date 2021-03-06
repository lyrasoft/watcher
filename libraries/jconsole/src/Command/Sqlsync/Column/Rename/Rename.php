<?php
/**
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Command\Sqlsync\Column\Rename;

use JConsole\Command\JCommand;
use Joomla\Registry\Registry;
use Sqlsync\Model\Column;
use Sqlsync\Model\Schema;

defined('JCONSOLE') or die;

/**
 * Class Rename
 *
 * @package     Joomla.Cli
 * @subpackage  JConsole
 *
 * @since       3.2
 */
class Rename extends JCommand
{
	/**
	 * An enabled flag.
	 *
	 * @var bool
	 */
	public static $isEnabled = true;

	/**
	 * Console(Argument) name.
	 *
	 * @var  string
	 */
	protected $name = 'rename';

	/**
	 * The command description.
	 *
	 * @var  string
	 */
	protected $description = 'Rename column';

	/**
	 * The usage to tell user how to use this command.
	 *
	 * @var string
	 */
	protected $usage = 'rename <cmd><table name></cmd> <cmd><column name></cmd> <option>[new column name]</option> <option>[option]</option>';

	protected $target = 'column name';

	/**
	 * Configure command information.
	 *
	 * @return void
	 */
	public function configure()
	{
		// $this->addArgument();
	}

	/**
	 * Execute this command.
	 *
	 * @return int|void
	 */
	protected function doExecute()
	{
		@$table = $this->input->args[0];

		@$name = $this->input->args[1];

		@$value = $this->input->args[2];

		if (!$table)
		{
			throw new \InvalidArgumentException("Missing argument <comment>1</comment>: Table name.\n\nUsage:\n" . $this->usage);
		}

		if (!$name)
		{
			throw new \InvalidArgumentException("Missing argument <comment>2</comment>: Column name.\n\nUsage:\n" . $this->usage);
		}

		$schemaModel = new Schema;

		/** @var $schema Registry */
		$schema = $schemaModel->load();

		// Get this column
		$column = (array) $schema->get($table . '.columns.' . $name);

		if (!$column)
		{
			throw new \UnexpectedValueException('We are not tracking this table or column of this table not exists.');
		}

		// Notice
		if (isset($column['From']))
		{
			$this->out()->out(sprintf('Current rename setting is: %s => %s', $column['From'], $column['Field']));
		}

		// If argument 3 not exists, get input.
		$value = $value ?: $this->out()->in('Enter new name:');

		// Not input, cancelled.
		if (!$value)
		{
			throw new \Exception('Cancelled.');
		}

		// Set input value to new name
		$column['Field'] = $value;

		// Remove origin
		$schema->set($table . '.columns.' . $name, $column);

		$schemaModel->save(null, $schema);

		$this->out()->out("Set rename to : " . $value);

		return true;
	}
}
