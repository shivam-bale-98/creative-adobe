<?php
namespace Concrete\Package\Formidable\Src\Formidable\Elements\Types;

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Form\Service\Form;
use Concrete\Package\Formidable\Src\Formidable\Elements\Element;
use Concrete\Core\Utility\Service\Validation\Strings;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\ValidationService;
use Concrete\Core\Http\Service\Json;
use Concrete\Package\Formidable\Src\Formidable\Uploader;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\File\File as ConcreteFile;
use Concrete\Core\File\Service\Application as FileService;
use Concrete\Core\File\Import\FileImporter;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Concrete\Core\File\Set\Set as FileSet;
use Concrete\Core\Utility\Service\Number;

class File extends Element {

    public function getName()
    {
        return t('File(s)');
    }

    public function getDescription()
    {
        return t('Upload files');
    }

    public function getEditableOptions()
    {
        $options = [
            // disable
            'option' => false,
            'view' => false,
            'css' => true,
            'default' => false,

            'view' => [
                'types' => [
                    'dropzone' => t('Dropzone'),
                    'file' => t('File (HTML)'),
                ]
            ],

            'range' => [
                'types' => [
                    'files' => t('files')
                ]
            ],

            'extensions' => true,
            'fileset' => true,
            'folder' => true,
            'filesize' => true,

            // others
            'dependencies' => [
                // available actions for itself
                'action' => [
                    'show',
                    'disable',
                    'class',
                ],
                // available conditions for other elements
                'condition' => [
                    'empty',
                    'not_empty'
                ]
            ]
        ];
        return array_merge(parent::getElementEditableOptions(), $options);
    }

    public function field()
    {
        $form = $this->app->make(Form::class);

        $handle = $this->element->getHandle();
        $value = $this->getPostData();

        $tags = parent::tags();

        $input = '';

        $view = (string)$this->element->getProperty('view', 'string');
        switch ($view) {
            case 'file':
                if ($this->element->getProperty('range', 'bool')) {
                    $element = [];
                    unset($tags['data-range-type'], $tags['data-range-min'], $tags['data-range-max']);
                    $min = (int)$this->element->getProperty('range_min', 'int');
                    $max = (int)$this->element->getProperty('range_max', 'int');
                    for ($i = 0; $i <= $max-1; $i++) {
                        $element[] = '<div class="'.$this->getHandle().' '.($i >= $min?'hide':'').' pb-2">';
                        $element[] = $form->file($handle.'[]', $tags);
                        $element[] = '</div>';
                    }

                    $input .= '<div data-range-type="files" data-range-min="'.$min.'" data-range-max="'.$max.'">';
                    $input .= @implode(PHP_EOL, $element);
                    $input .= '<div class="additional-file">';
                    $input .= '<button type="button" class="btn btn-secondary btn-sm no-valid" data-additional-file>'.t('Add another file').'</button>';
                    $input .= '</div>';
                    $input .= '</div>';
                }
                else {
                    $input = $form->file($handle, $tags);
                }
            break;

            case 'dropzone':

                $tags = parent::tags();

                $extensions = $this->getAllowedExtensions();
                $extensions = array_map(function($ext) { return '.'.$ext; }, $extensions);

                // update classes
                $class = '';
                if (isset($tags['class'])) {
                    $class .= ' '.$tags['class'];
                }

                $max = (int)$this->element->getProperty('range_max', 'int');
                if ($max == 0) {
                    $max = 99;
                }

                $filesize = $this->getMaxFilesize();
                if (empty($filesize)) {
                    $filesize = 5; // default is 5MB each file...
                }

                $input .= '<div class="formidable-dropzone border-dashed" data-element-handle="'.$handle.'">';
                //$input .= '<div class="message alert hide"></div>';
                $input .= '<div id="'.$handle.'" class="dropzone-box'.$class.'" data-range-max="'.$max.'" data-extensions="'.@implode(',', $extensions).'" data-filesize="'.$filesize.'">';
                $input .= '<div class="dz-message">';
                $input .= t('PDF format. Max 2mb size');
                $input .= '</div>';
                $input .= '<div class="fallback">';
                $input .= $form->file($handle.'[]', $tags);
                $input .= '</div>';
                $input .= '</div>';


                $input .= '<div id="dropzoneTemplate" style="display: none;">';
                $input .= '<div class="row d-flex align-items-center mb-1 dropzone-item">';

                // $input .= '<div class="col-auto">';
                // $input .= '<img class="img-fluid img-thumbnail" data-dz-thumbnail>';
                // $input .= '</div>';

                $input .= '<div class="col">';
                $input .= '<div class="dz-filename"><span data-dz-name></span> (<span data-dz-size></span>)</div>';
                $input .= '<div class="dz-progress">';
                $input .= '<div class="dz-upload progress-bar bg-success" role="progressbar" style="width: 0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress=""></div>';
                $input .= '</div>';
                $input .= '<div class="dz-error-message"><span data-dz-errormessage></span></div>';
                $input .= '</div>';

                $input .= '<div class="col-auto">';
                $input .= '<a href="javascript:;" class="btn btn-sm btn-danger" data-dz-remove><i class="fa fa-times"></i></a>';
                $input .= '</div>';

                $input .= '</div>';
                $input .= '</div>';

                $input .= '</div>';
            break;
        }

        $this->loadAsset();

        return $input;
    }

    public function validate()
    {
        $n = $this->app->make(Number::class);
        $e = $this->app->make(ErrorList::class);
        $str = $this->app->make(Strings::class);
        $fls = $this->app->make(ValidationService::class);

        $data = $this->getPostData();

        // it true, there is an active dependecy rule.
        // if active, skip validation
        if ($this->skipByDependency($data)) {
            return $e;
        }

        $view = (string)$this->element->getProperty('view', 'string');
        switch ($view) {

            case 'file':

                // Errors for $_FILES
                /*
                UPLOAD_ERR_OK: 0
                UPLOAD_ERR_INI_SIZE: 1
                UPLOAD_ERR_FORM_SIZE: 2
                UPLOAD_ERR_NO_TMP_DIR: 6
                UPLOAD_ERR_CANT_WRITE: 7
                UPLOAD_ERR_EXTENSION: 8
                UPLOAD_ERR_PARTIAL: 3
                */

                // prepare upload
                $filesize = 0;
                if ($this->element->getProperty('filesize', 'bool')) {
                    $filesize = $n->getBytes($this->element->getProperty('filesize_value', 'float').'M');
                }

                $data = $this->getFileData($this->element->getHandle());

                // validate submission
                if ($this->element->isRequired()) {
                    if ($this->element->getProperty('range', 'bool')) {
                        $min = (int)$this->element->getProperty('range_min', 'int');
                        $max = (int)$this->element->getProperty('range_max', 'int');
                        $options = !empty($data)?count($data):0;
                        if ($options < $min || $options > $max) {
                            $e->add(t('Field "%s" should have between %s and %s files uploaded', $this->element->getName(), $min, $max));
                        }
                    }
                    else {
                        if (!is_object($data)) {
                            $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
                        }
                    }
                }

                // validate extensions
                if ($this->element->getProperty('range', 'bool')) {
                    foreach ((array)$data as $file) {
                        if (is_object($file) && !$fls->extension($file->getClientOriginalName())) {
                            $e->add(t('Field "%s" is invalid extension', $this->element->getName()));
                        }
                        if (is_object($file) && !$str->notempty($file->getSize()) && $filesize > 0 && $filesize < $file->getSize()) {
                            $e->add(t('Field "%s" is too large (max. %sMB allowed)', $this->element->getName(), $filesize/1024/1024));
                        }
                    }
                }
                else {
                    if (is_object($data) && !$fls->extension($data->getClientOriginalName())) {
                        $e->add(t('Field "%s" is invalid extension', $this->element->getName()));
                    }
                    if (is_object($data) && !$str->notempty($data->getSize()) && $filesize > 0 && $filesize < $data->getSize()) {
                        $e->add(t('Field "%s" is too large (max. %sMB allowed)', $this->element->getName(), $filesize/1024/1024));
                    }
                }

            break;

            case 'dropzone':

                if ($this->element->isRequired()) {
                    if ($this->element->getProperty('range', 'bool')) {
                        $min = (int)$this->element->getProperty('range_min', 'int');
                        $max = (int)$this->element->getProperty('range_max', 'int');
                        $options = !empty($data)?count($data):0;
                        if ($options < $min || $options > $max) {
                            $e->add(t('Field "%s" should have between %s and %s files uploaded', $this->element->getName(), $min, $max));
                        }
                    }
                    else {
                        if (!count((array)$data)) {
                            $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
                        }
                    }
                }
                // do cleanup on validation?
                // $this->removeMocks();

            break;
        }

        return $e;
    }


    public function upload()
    {
	    $e = $this->app->make(ErrorList::class);
        $fls = $this->app->make(ValidationService::class);

        $data = $this->getFileData('file');
        if (!is_object($data)) {
            $e->add(t('Field "%s" is empty or invalid', $this->element->getName()));
        }

		if (!$fls->extension($data->getClientOriginalName(), $this->getAllowedExtensions())) {
            $e->add(t('Field "%s" is invalid extension', $this->element->getName()));
        }

        if ($e->has()) {
            return [
                'errors' => $e->getList(),
            ];
        }

		$file = pathinfo($data->getClientOriginalName());
		$filename = $file['filename'].'-'.date("YmdHis").'.'.$file['extension'];

        // now upload in tmp directory
		$response = Uploader::upload($data->getPathname(), $filename, '/formidable/'.$this->element->getItemID().'/');
        if ($response['success']) {
            $current = $this->data->session($this->element->getHandle());
            $this->data->set($this->element->getHandle(), array_merge((array)$current, (array)$response['file']));
        }
		return $response;
	}


	public function delete()
    {
        $e = $this->app->make(ErrorList::class);
        $str = $this->app->make(Strings::class);
        $fls = $this->app->make(ValidationService::class);

        $data = $this->app['request']->post();

        if (!$str->notempty($data['file'])) {
            $e->add(t('File doesn\'t exists or invalid'));
        }

        if (!$fls->extension($data['file'], $this->getAllowedExtensions())) {
            $e->add(t('Field "%s" is invalid extension', $this->element->getName()));
        }

        if (!$fls->file(DIR_APPLICATION.'/files/tmp/formidable/'.$this->element->getItemID().'/'.$data['file'])) {
            $e->add(t('Field "%s" doesn\'t exists.', $this->element->getName()));
        }

        if ($e->has()) {
            return [
                'errors' => $e->getList(),
                'file' => $data['file'],
            ];
        }

		// now delete from tmp directory
		$response = Uploader::delete($data['file'], '/formidable/'.$this->element->getItemID().'/');
        if (isset($response['success'])) {
            $current = $this->data->session($this->element->getHandle());
            if (isset($response['file']) && is_array($current) && count($current)) {
                unset($current[array_search($response['file'], (array)$current)]);
            }
            $this->data->set($this->element->getHandle(), $current);
        }
		return $response;
    }

    // display value formater
    public function getDisplayData($data, $format = 'plain')
    {
        if (!is_array($data) || !count($data)) {
            return '';
        }

        $options = [];
        foreach ($data as $v) {
            $file = ConcreteFile::getByID($v['fileID']);
            if ($format == 'object') {
                if ($file) {
                    $options[] = $file;
                }
                continue;
            }
            if ($format == 'plain') {
                $options[] = $v['filename'];
                continue;
            }
            if (!$file) {
                $options[] = t('<div>%s (%s) (removed file)<div>', $v['filename'], $v['filesize']);
                continue;
            }
            $fv = $file->getApprovedVersion();
            $options[] = t('<div><a href="%s">%s</a> (%s)<div>', $fv->getForceDownloadURL(), $fv->getFileName(), $fv->getSize());
        }

        if ($format == 'object') {
            return $options;
        }
        if ($format == 'plain') {
            return @implode(', ', $options);
        }
        return @implode(PHP_EOL, $options);
    }


    // process data after successful submitted
    public function getProcessedData()
    {
        $files = [];

        $data = $this->getFiles();

        foreach ($data as $file) {

            $path = DIR_APPLICATION.'/files/tmp/formidable/'.$this->element->getItemID().'/'.$file['name'];
            if (file_exists($path)) {

                $importer = $this->app->make(FileImporter::class);
                $result = $importer->importLocalFile($path);
                if (!is_object($result)) {
                    // TODO?
                    // what to do with an error?
                    continue;
                }

                // move to folder
                if ($this->element->getProperty('folder', 'bool')) {
                    $folder = FileFolder::getByID($this->element->getProperty('folder_value', 'int'));
                    if ($folder) {
                        $file_node = $result->getFile()->getFileNodeObject();
                        if (is_object($file_node)) {
                            $file_node->move($folder);
                        }
                    }
                }

                if ($this->element->getProperty('fileset', 'bool')) {
                    $sets = $this->element->getProperty('fileset_value', 'array');
                    foreach ((array)$sets as $sID) {
                        $fs = FileSet::getByID($sID);
                        if (is_object($fs)) {
                            $fs->addFileToSet($result);
                        }
                    }

                }

                // Remove tmp file
                Uploader::delete($file['name'], '/formidable/'.$this->element->getItemID().'/');

                $files[] = array(
                    'fileID' => $result->getFileID(),
                    'filename' => $file['name'],
                    'extension' => $result->getExtension(),
                    'filesize' => $result->getSize()
                );
            }
        }

        return $files;
    }


    private function loadAsset()
    {
        $as = AssetList::getInstance();

        $handle = $this->element->getHandle();

        $view = $this->element->getProperty('view', 'string');

        switch ($view) {

            case 'file':
                // noting to do...
            break;

            case 'dropzone':

                // current files
                $files = (new Json)->encode($this->getFiles());

                $javascript = <<<JAVASCRIPT
                    var dropzone_%e;
                    $(function() {
                        dropzone_%e = $('div[id="%e"]', '[id="formidable_%z"]').formidableDropzone(formidable_%z, %f);
                    });
                JAVASCRIPT;
                $javascript = str_replace(['%e', '%f', '%z'], [$handle, $files, $this->element->getForm()->getHandle()], $javascript);

                $as->register('javascript', 'formidable/dropzone', 'js/plugins/dropzone.min.js', ['minify' => false, 'combine' => true], $this->getPackageHandle());
                $as->register('javascript-inline', 'file-'.$handle.'-'.$view, $javascript, ['minify' => true, 'combine' => true], $this->getPackageHandle());

                $this->element->registerAsset('javascript', 'formidable/dropzone');
                $this->element->registerAsset('javascript-inline', 'file-'.$handle.'-'.$view);


            break;
        }
    }

    private function getFiles()
    {
        $fls = $this->app->make(ValidationService::class);

        $files = [];
        $values = $this->getPostData();

        $view = $this->element->getProperty('view', 'string');

        switch ($view) {

            case 'file':

                // get the files
                $data = $this->getFileData();
                if (!$this->element->getProperty('range', 'bool')) {
                    $data = [$data]; // change to array
                }

                // upload
                foreach ($data as $f) {

                    if (empty($f->getClientOriginalName())) {
                        continue;
                    }
                    $file = pathinfo($f->getClientOriginalName());
                    $filename = $file['filename'].'-'.date("YmdHis").'.'.$file['extension'];

                    // now upload in tmp directory
                    $response = Uploader::upload($f->getPathname(), $filename, '/formidable/'.$this->element->getItemID().'/');
                    if ($response['success']) {
                        $files[] = array(
                            'name' => $response['file'],
                            'type' => '',
                            'size' => Uploader::size($response['file'], '/formidable/'.$this->element->getItemID().'/')
                        );
                    }
                }

            break;

            case 'dropzone':
                foreach ((array)$values as $value) {

                    if (empty($value)) {
                        continue;
                    }

                    if (!$fls->file(DIR_APPLICATION.'/files/tmp/formidable/'.$this->element->getItemID().'/'.$value)) {
                        continue;
                    }

                    $files[] = array(
                        'name' => $value,
                        'type' => '',
                        'size' => Uploader::size($value, '/formidable/'.$this->element->getItemID().'/')
                    );
                }
            break;
        }
        return $files;
	}

    private function getAllowedExtensions()
    {
        if ($this->getProperty('extensions', 'bool')) {
            return array_map('trim', explode(',', $this->getProperty('extensions_value', 'array')));
        }
        return $this->app->make(FileService::class)->getAllowedFileExtensions();
    }

    private function getMaxFilesize($display = false, $type = 'MB')
    {
        $nh = $this->app->make(Number::class);

        $filesize = 0;

        if ($this->element->getProperty('filesize', 'bool')) {
            $filesize = $this->element->getProperty('filesize_value', 'float');
        }

        if (empty($filesize)) {
            $filesize = ini_get('upload_max_filesize');
            if ($filesize != 0) {
                $filesize = (int)$filesize;
            }
        }

        if ($display) {
            return $filesize.' '.$type;
        }
        return $filesize;
    }
}