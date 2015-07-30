<?php

namespace Redking\Bundle\OAuthBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Redking\Bundle\OAuthBundle\Document\Client;

class ClientAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', null, array('identifier' => true))
            ->add('allowedGrantTypes')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-6'))
                ->add('name')
                ->add('allowedGrantTypes', 'choice', array('choices' => Client::getGrantChoices(), 'multiple' => true))
            ->end()
            ->with('URL', array('class' => 'col-md-6'))
                ->add('redirectUris', 'sonata_type_native_collection', array('type' => 'text', 'allow_add' => true, 'allow_delete' => true))
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('randomId')
            ->add('redirectUris')
            ->add('secret')
            ->add('allowedGrantTypes')
            ->add('id')
            ->add('name')
        ;
    }
}
