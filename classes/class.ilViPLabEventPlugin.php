<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once './Services/EventHandling/classes/class.ilEventHookPlugin.php';

/**
 * viplab event plugin base class
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 */
class ilViPLabEventPlugin extends ilEventHookPlugin
{
	private static $instance = null;

	const PNAME = 'ViPLabEvent';
	const CTYPE = 'Services';
	const CNAME = 'EventHandling';
	const SLOT_ID = 'evhk';

	/**
	 * Get singelton instance
	 * @global ilPluginAdmin $ilPluginAdmin
	 * @return ilFhoevImportPlugin
	 */
	public static function getInstance()
	{
		global $ilPluginAdmin;

		if (self::$instance)
		{
			return self::$instance;
		}
		include_once './Services/Component/classes/class.ilPluginAdmin.php';
		return self::$instance = ilPluginAdmin::getPluginObject(
					self::CTYPE, 
					self::CNAME, 
					self::SLOT_ID, 
					self::PNAME
		);
	}
	
	/**
	 * Handle event
	 * @param type $a_component
	 * @param type $a_event
	 * @param type $a_parameter
	 */
	public function handleEvent($a_component, $a_event, $a_parameter)
	{
		ilLoggerFactory::getLogger('viplabevent')->debug('New event: '. $a_event.' from component: ' .$a_component);
		
		if($a_component == 'Services/WebServices/ECS' and $a_event == 'newEcsEvent')
		{
			// redirect event to viplab plugin 
			$active = $GLOBALS['ilPluginAdmin']->getActivePluginsForSlot(
				IL_COMP_MODULE,
				'TestQuestionPool',
				'qst'
			);
			foreach($active as $num => $info)
			{
				if($info == 'assViPLab')
				{
					$obj = ilPluginAdmin::getPluginObject(
						IL_COMP_MODULE,
						'TestQuestionPool',
						'qst', 
						$info
					);
					
					if($obj instanceof ilassViPLabPlugin )
					{
						$obj->handleEcsEvent($a_event, $a_parameter);
					}
				}
			}
		}
	}
	

	/**
	 * Get plugin name
	 * @return string
	 */
	public function getPluginName()
	{
		return self::PNAME;
	}

	/**
	 * Init auto load
	 */
	protected function init()
	{
		$this->initAutoLoad();
	}

	/**
	 * Init auto loader
	 * @return void
	 */
	protected function initAutoLoad()
	{
		spl_autoload_register(
				array($this, 'autoLoad')
		);
	}
	
	

	/**
	 * Auto load implementation
	 *
	 * @param string class name
	 */
	private final function autoLoad($a_classname)
	{
		$class_file = $this->getClassesDirectory() . '/class.' . $a_classname . '.php';
		if (@include_once($class_file))
		{
			return;
		}
	}
	

}
?>