<?php

use Adianti\Database\TRecord;

/**
 * CanalAtendimento Active Record
 * @author  <your-name-here>
 */
class CanalAtendimento extends TRecord
{
    const TABLENAME = 'canal_atendimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    private $atendimento;
    private $canal;
    
    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('atendimento_id');
        parent::addAttribute('canal_id');
    }

    
    /**
     * Method set_atendimento
     * Sample of usage: $var->atendimento = $object;
     * @param $object Instance of Atendimento
     */
    public function set_atendimento(Atendimento $object)
    {
        $this->atendimento = $object;
        $this->atendimento_id = $object->id;
    }
    
    /**
     * Method get_atendimento
     * Sample of usage: $var->atendimento->attribute;
     * @returns Atendimento instance
     */
    public function get_atendimento()
    {
        
        // loads the associated object
        if (empty($this->atendimento))
            $this->atendimento = new Atendimento($this->atendimento_id);
        
        // returns the associated object
        return $this->atendimento;
    }
    /**
     * Method set_canal
     * Sample of usage: $var->canal = $object;
     * @param $object Instance of Canal
     */
    public function set_canal(Canal $object)
    {
        $this->canal = $object;
        $this->canal_id = $object->id;
    }
    
    /**
     * Method get_canal
     * Sample of usage: $var->canal->attribute;
     * @returns Canal instance
     */
    public function get_canal()
    {
        
        // loads the associated object
        if (empty($this->canal))
            $this->canal = new Canal($this->canal_id);
        
        // returns the associated object
        return $this->canal;
    }
    

}
