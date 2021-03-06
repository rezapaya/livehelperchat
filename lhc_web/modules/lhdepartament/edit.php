<?php

$tpl = erLhcoreClassTemplate::getInstance('lhdepartament/edit.tpl.php');

$Departament = erLhcoreClassDepartament::getSession()->load( 'erLhcoreClassModelDepartament', (int)$Params['user_parameters']['departament_id'] );

if ( isset($_POST['Cancel_departament']) ) {
    erLhcoreClassModule::redirect('departament/departaments');
    exit;
}

if ( isset($_POST['Delete_departament']) ) {

	if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
		erLhcoreClassModule::redirect('departament/departaments');
		exit;
	}

	$Departament->removeThis();

    erLhcoreClassModule::redirect('departament/departaments');
    exit;
}



if (isset($_POST['Update_departament']) || isset($_POST['Save_departament'])  )
{
   $definition = array(
        'Name' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Email' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'unsafe_raw'
        ),
        'Identifier' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'string'
        ),
        'Priority' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int'
        ),
        'TansferDepartmentID' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int',array('min_range' => 1)
        ),
        'TransferTimeout' => new ezcInputFormDefinitionElement(
            ezcInputFormDefinitionElement::OPTIONAL, 'int', array('min_range' => 5)
        )
    );

    $form = new ezcInputForm( INPUT_POST, $definition );
    $Errors = array();

    if ( !$form->hasValidData( 'Name' ) || $form->Name == '' )
    {
        $Errors[] =  erTranslationClassLhTranslation::getInstance()->getTranslation('departament/edit','Please enter a department name');
    }

    if ( $form->hasValidData( 'TansferDepartmentID' ) )
    {
        $Departament->department_transfer_id = $form->TansferDepartmentID;
    } else {
    	$Departament->department_transfer_id = 0;
    }

    if ( $form->hasValidData( 'Identifier' ) )
    {
        $Departament->identifier = $form->Identifier;
    }

    if ( $form->hasValidData( 'TransferTimeout' ) )
    {
        $Departament->transfer_timeout = $form->TransferTimeout;
    } else {
    	$Departament->transfer_timeout = 0;
    }

    if (!isset($_POST['csfr_token']) || !$currentUser->validateCSFRToken($_POST['csfr_token'])) {
     	erLhcoreClassModule::redirect('departament/departaments');
    	exit;
    }

    if ( $form->hasValidData( 'Email' ) ) {

    	$partsEmail = explode(',', $form->Email);
    	$validatedEmail = array();
    	foreach ($partsEmail as $email){
    		if (filter_var(trim($email), FILTER_VALIDATE_EMAIL)){
    			$validatedEmail[] = trim($email);
    		}
    	}

        $Departament->email = implode(',', $validatedEmail);

    } else {
    	$Departament->email = '';
    }

    if ( $form->hasValidData( 'Priority' ) ) {
        $Departament->priority = $form->Priority;
    } else {
    	$Departament->priority = 0;
    }

    if (count($Errors) == 0)
    {
        $Departament->name = $form->Name;

        erLhcoreClassDepartament::getSession()->update($Departament);

        if (isset($_POST['Save_departament'])) {
            erLhcoreClassModule::redirect('departament/departaments');
            exit;
        } else {
            $tpl->set('updated',true);
        }

    }  else {
        $tpl->set('errors',$Errors);
    }
}

$tpl->set('departament',$Departament);

$Result['content'] = $tpl->fetch();

$Result['path'] = array(
array('url' => erLhcoreClassDesign::baseurl('system/configuration'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','System configuration')),
array('url' => erLhcoreClassDesign::baseurl('departament/departaments'),'title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','departments')),
array('title' => erTranslationClassLhTranslation::getInstance()->getTranslation('department/edit','Edit a department').' - '.$Departament->name),);

?>