<?php

require LIBPATH.'/QuickFW/Module.php';

class QuickFW_PlainView 
{
    protected $_vars;
    protected $_tmplPath;
    protected $plugins;
    
    public $_mainTmpl;
    
    public function __construct($tmplPath = null, $mainTmpl = 'main.tpl')
    {
        $this->_vars = array();
        if (null !== $tmplPath) {
            $this->_tmplPath = $tmplPath;
        }

        require LIBPATH.'/QuickFW/Plugs.php';
        $this->plugins = QuickFW_Plugs::getInstance();

        $this->_mainTmpl = $mainTmpl;
    }
    
    public function assign($spec, $value = null)
    {
        if (is_array($spec))
        {
            $this->_var = array_merge($this->_vars, $spec);
        }
        else 
            $this->_vars[$spec] = $value;
    }
    
    public function delete($spec)
    {
        if (is_array($spec))
        {
            foreach ($spec as $item)
                $this->delete($item);
        }
        else 
            if (isset($this->_vars[$spec]))
                unset($this->_vars[$spec]);
    }
    
    public function getTemplateVars($var = null)
    {
        if ($var === null)
            return $_vars;
        else if (isset($_vars[$var]))
            return $_vars[$var];
        else return null;
    }
    
    public function setScriptPath($path)
    {
        if (is_readable($path)) {
            $this->_tmplPath = $path;
            return;
        }
    }
    
    public function module($module)
    {
        $result = '';
        /*$argnum = func_num_args();
        $args = func_get_args();
        $module = array_shift($args);
        array_unshift($args, &$this);
        array_unshift($args, &$result);
        array_unshift($args, $module);*/
        
        //call_user_func_array(array('QuickFW_Module', 'getTemplate'), $args);
        QuickFW_Module::getTemplate($module, $result, $this);
        return $result;
    }
    
    public function render($tmpl)
    {
        extract($this->_vars, EXTR_OVERWRITE);
        error_reporting(E_ALL ^ E_NOTICE);
        $P=&$this->plugins;
        ob_start();
        include($this->_tmplPath . '/' . $tmpl);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
    
    /*public function display($tpl)
    {
        global $config;
        $content = $this->render($tpl);
        //QuickFW_Cacher::set(generateLabel(), $content);
        echo $content;
    }*/
    
	public function displayMain()
	{
        //global $config;
        $content = $this->render($this->_mainTmpl);
        //getCache()->set(generateLabel(), $content);
        //QuickFW_Cacher::set(generateLabel(), $content);
        $content = $this->plugins->HeaderFilter($content);
        echo $content;
	}
    
}

?>