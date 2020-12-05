<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBMultiSearch;
use Adianti\Widget\Wrapper\TDBUniqueSearch;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * AtendimentoForm Registration
 */
class AtendimentoForm extends TPage
{
    private $form; // form
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Atendimento');
        $this->form->setClientValidation(true);
        $this->form->setFormTitle('Atendimento');
        
        // create the form fields
        $id       = new THidden('id');
        $descricao     = new TEntry('descricao');
        $cliente_id     = new TDBUniqueSearch('cliente_id', 'unit_a', 'Cliente', 'id', 'nome');
        $data_atend     = new TDate('data_atend');
        $canals    = new TDBMultiSearch('canals', 'unit_a', 'Canal', 'id', 'nome');
        $ordem     = new THidden('ordem');
        $etapa_atendimento_id     = new THidden('etapa_atendimento_id');
        $prioridade_id = new TDBCombo('prioridade_id', 'unit_a', 'Prioridade', 'id', 'nome');
        $id->setEditable(FALSE);

        $data_atend->setDatabaseMask('yyyy-mm-dd');
        $data_atend->setMask('dd/mm/yyyy');
        $cliente_id->setMinLength(1);
        $canals->setMinLength(1);
        
        // add the form fields
        $this->form->addFields( [$id, $ordem, $etapa_atendimento_id] );
        $this->form->addFields( [new TLabel('Descrição')],  [$descricao] );
        $this->form->addFields( [new TLabel('Cliente')], [$cliente_id] );
        $this->form->addFields( [new TLabel('Data')], [$data_atend] );
        $this->form->addFields( [new TLabel('Canal')], [$canals] );
        $this->form->addFields( [new TLabel('Prioridade')], [$prioridade_id] );
        
        $descricao->addValidation('Descrição', new TRequiredValidator);
        $cliente_id->addValidation('Cliente', new TRequiredValidator);
        $data_atend->addValidation('Data', new TRequiredValidator);
        $prioridade_id->addValidation('Prioridade', new TRequiredValidator);
        
        // define the form action
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addActionLink('Limpar',  new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addActionLink('Voltar',  new TAction(['AtendimentoDataGridView', 'onReload']), 'fa:table blue');
        
        // wrap the page content using vertical box
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add($this->form);
        parent::add($vbox);
    }
    /**
     * method onSave()
     * Executed whenever the user clicks at the save button
     */
    function onSave()
    {
        try
        {
            // open a transaction with database 'unit_a'
            TTransaction::open('unit_a');
            
            $data = $this->form->getData(); // get form data as array
            
            $object = new Atendimento;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            if(empty($object->id))
            {
                $object->ordem = 0;
                $object->etapa_atendimento_id = 1;
            }
            
            $this->form->validate(); // run form validation
            
            $object->clearParts();
            
            if( !empty($data->canals) )
            {
                foreach( $data->canals as $canal_id )
                {
                    $object->addCanal( new Canal($canal_id) );
                }
            }
            $object->store(); // save the object
            $data->id = $object->id;
            // fill the form with the active record data
            $this->form->setData($data);

            TTransaction::close();  // close the transaction
            
            // shows the success message
            new TMessage('info', 'Atendimento salvo', new TAction(['AtendimentoDataGridView', 'onReload']));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form
     */
    public function onClear()
    {
        $this->form->clear( TRUE );
    }
    
    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $key = $param['id'];  // get the parameter
                TTransaction::open('unit_a');   // open a transaction with database 'unit_a'
                $object = new Atendimento($key);        // instantiates object 
                
                $canals = array();
                if( $canals_db = $object->getCanals() )
                {
                    foreach( $canals_db as $canal )
                    {
                        $canals[] = $canal->id;
                    }
                }
                $object->canals = $canals;

                $this->form->setData($object);   // fill the form with the active record data
                TTransaction::close();           // close the transaction
            }
            else
            {
                $this->form->clear( true );
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}