<?php

/**
 *
 * @package Plugin
 * @subpackage PIFA Form Asistant
 * @version SVN Revision $Rev:$
 * @author marcus.gnass
 * @copyright four for business AG
 * @link http://www.4fb.de
 */

//cInclude('classes', 'class.util.helper.php');

/**
 *
 * @author marcus.gnass
 */
class DefaultFormModule extends PifaAbstractFormModule {

    /**
     *
     * @param array $values
     * @param array $errors TODO not yet implemented
     * @throws Exception if form could not be loaded
     */
    protected function doGet(array $values = array(), array $errors = array()) {

        // set template to use
        $this->setTemplateName($this->getSetting('pifaform_template_get'));

        // create and load form
        $pifaForm = new PifaForm($this->getIdform());

        // catch error
        if (true !== $pifaForm->isLoaded()) {
            throw new PifaException('form could not be loaded');
        }

        // set values (keep default values if NULL!)
        $pifaForm->setValues($values);

        // set errors
        $pifaForm->setErrors($errors);

        // assign rendered form
        $this->getTpl()->assign('form', $pifaForm->toHtml(array(
            'action' => cUri::getInstance()->build(array(
                'idart' => cRegistry::getArticleId(),
                'lang' => cRegistry::getLanguageId()
            ), true)
        )));

    }

    /**
     *
     * @see PifaAbstractFormModule::doPost()
     */
    protected function doPost() {

        // set template to use
        $this->setTemplateName($this->getSetting('pifaform_template_post'));

        // create processor and pass module in order to access its settings
        // processorClass is subclass of
        try {
            // get name of processor class
            $processorClass = $this->getSetting('pifaform_processor');
            // get name of file in which processor class could be found
            $filename = Pifa::fromCamelCase($processorClass);
            $filename = "extensions/class.pifa.$filename.php";
            if (false === file_exists(Pifa::getPath() . $filename)) {
                throw new PifaException('missing processor file ' . $filename);
            }
            plugin_include(Pifa::getName(), $filename);
            if (false === class_exists($processorClass)) {
                throw new PifaException('missing processor class ' . $processorClass);
            }
            // create processor instance
            $postProcessor = new $processorClass($this);
            // process processor
            $postProcessor->process();
            // assign reply to post template
            $this->getTpl()->assign('reply', $this->label['reply']);
        } catch (PifaValidationException $e) {
            // display form with valid values again
            $this->doGet($postProcessor->getForm()->getValues(), $e->getErrors());
        } catch (Exception $e) {
            Pifa::logException($e);
            Pifa::displayException($e);
        }

    }

}
