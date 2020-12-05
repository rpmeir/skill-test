<?php

use Adianti\Control\TAction;
use Adianti\Control\TPage;
use Adianti\Database\TTransaction;
use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Container\TVBox;
use Adianti\Widget\Dialog\TMessage;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TFile;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapFormBuilder;

/**
 * ClienteForm Registration
 */
class ClienteForm extends TPage
{
    private $form; // form
    
    use Adianti\Base\AdiantiFileSaveTrait;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new BootstrapFormBuilder('form_Cliente');
        $this->form->setClientValidation(true);
        $this->form->setFormTitle('Cliente');
        
        // create the form fields
        $id       = new THidden('id');
        $foto     = new TFile('foto');
        $nome     = new TEntry('nome');
        $data_nasc     = new TDate('data_nasc');
        $id->setEditable(FALSE);

        $data_nasc->setDatabaseMask('yyyy-mm-dd');
        $data_nasc->setMask('dd/mm/yyyy');
        
        // add the form fields
        $this->form->addFields( [$id] );
        $this->form->addFields( [new TLabel('Foto')],  [$foto] );
        $this->form->addFields( [new TLabel('Nome')], [$nome] );
        $this->form->addFields( [new TLabel('Data Nascimento')], [$data_nasc] );

        $foto->enableFileHandling();
        $foto->enableImageGallery();
        
        $foto->addValidation('Foto', new TRequiredValidator);
        $nome->addValidation('Nome', new TRequiredValidator);
        $data_nasc->addValidation('Data Nascimento', new TRequiredValidator);
        
        // define the form action
        $this->form->addAction('Salvar', new TAction([$this, 'onSave'], ['static' => '1']), 'fa:save green');
        $this->form->addActionLink('Limpar',  new TAction([$this, 'onClear']), 'fa:eraser red');
        $this->form->addActionLink('Voltar',  new TAction(['ClienteDataGridView', 'onReload']), 'fa:table blue');
        
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
            
            $object = new Cliente;  // create an empty object
            $object->fromArray( (array) $data); // load the object with data
            
            $this->form->validate(); // run form validation
            $object->store(); // save the object
            $data->id = $object->id;
            // fill the form with the active record data
            $this->form->setData($data);

            // copy file to target folder
            $this->saveFile($object, $data, 'foto', 'files/clientes/');

            TTransaction::close();  // close the transaction
            
            // shows the success message
            new TMessage('info', 'Cliente salvo', new TAction(['ClienteDataGridView', 'onReload']));
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
                $object = new Cliente($key);        // instantiates object 
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