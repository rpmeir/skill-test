<?php

use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;

/**
 * Atendimento Active Record
 * @author  <your-name-here>
 */
class Atendimento extends TRecord
{
    const TABLENAME = 'atendimento';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}
    
    
    private $etapa_atendimento;
    private $cliente;
    private $prioridade;
    private $canals;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('descricao');
        parent::addAttribute('data_atend');
        parent::addAttribute('ordem');
        parent::addAttribute('etapa_atendimento_id');
        parent::addAttribute('cliente_id');
        parent::addAttribute('prioridade_id');
    }

    
    /**
     * Method set_etapa_atendimento
     * Sample of usage: $atendimento->etapa_atendimento = $object;
     * @param $object Instance of EtapaAtendimento
     */
    public function set_etapa_atendimento(EtapaAtendimento $object)
    {
        $this->etapa_atendimento = $object;
        $this->etapa_atendimento_id = $object->id;
    }
    
    /**
     * Method get_etapa_atendimento
     * Sample of usage: $atendimento->etapa_atendimento->attribute;
     * @returns EtapaAtendimento instance
     */
    public function get_etapa_atendimento()
    {
        // loads the associated object
        if (empty($this->etapa_atendimento))
            $this->etapa_atendimento = new EtapaAtendimento($this->etapa_atendimento_id);
    
        // returns the associated object
        return $this->etapa_atendimento;
    }
    
    
    /**
     * Method set_cliente
     * Sample of usage: $atendimento->cliente = $object;
     * @param $object Instance of Cliente
     */
    public function set_cliente(Cliente $object)
    {
        $this->cliente = $object;
        $this->cliente_id = $object->id;
    }
    
    /**
     * Method get_cliente
     * Sample of usage: $atendimento->cliente->attribute;
     * @returns Cliente instance
     */
    public function get_cliente()
    {
        // loads the associated object
        if (empty($this->cliente))
            $this->cliente = new Cliente($this->cliente_id);
    
        // returns the associated object
        return $this->cliente;
    }
    
    
    /**
     * Method set_prioridade
     * Sample of usage: $atendimento->prioridade = $object;
     * @param $object Instance of Prioridade
     */
    public function set_prioridade(Prioridade $object)
    {
        $this->prioridade = $object;
        $this->prioridade_id = $object->id;
    }
    
    /**
     * Method get_prioridade
     * Sample of usage: $atendimento->prioridade->attribute;
     * @returns Prioridade instance
     */
    public function get_prioridade()
    {
        // loads the associated object
        if (empty($this->prioridade))
            $this->prioridade = new Prioridade($this->prioridade_id);
    
        // returns the associated object
        return $this->prioridade;
    }
    
    
    /**
     * Method addCanal
     * Add a Canal to the Atendimento
     * @param $object Instance of Canal
     */
    public function addCanal(Canal $object)
    {
        $this->canals[] = $object;
    }
    
    /**
     * Method getCanals
     * Return the Atendimento' Canal's
     * @return Collection of Canal
     */
    public function getCanals()
    {
        return $this->canals;
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->canals = array();
    }

    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
    
        // load the related Canal objects
        $repository = new TRepository('CanalAtendimento');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('atendimento_id', '=', $id));
        $canal_atendimentos = $repository->load($criteria);
        if ($canal_atendimentos)
        {
            foreach ($canal_atendimentos as $canal_atendimento)
            {
                $canal = new Canal( $canal_atendimento->canal_id );
                $this->addCanal($canal);
            }
        }
    
        // load the object itself
        return parent::load($id);
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();
    
        // delete the related CanalAtendimento objects
        $criteria = new TCriteria;
        $criteria->add(new TFilter('atendimento_id', '=', $this->id));
        $repository = new TRepository('CanalAtendimento');
        $repository->delete($criteria);
        // store the related CanalAtendimento objects
        if ($this->canals)
        {
            foreach ($this->canals as $canal)
            {
                $atendimento_canal = new CanalAtendimento;
                $atendimento_canal->canal_id = $canal->id;
                $atendimento_canal->atendimento_id = $this->id;
                $atendimento_canal->store();
            }
        }
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        $id = isset($id) ? $id : $this->id;
        // delete the related CanalAtendimento objects
        $repository = new TRepository('CanalAtendimento');
        $criteria = new TCriteria;
        $criteria->add(new TFilter('atendimento_id', '=', $id));
        $repository->delete($criteria);
        
    
        // delete the object itself
        parent::delete($id);
    }


}
