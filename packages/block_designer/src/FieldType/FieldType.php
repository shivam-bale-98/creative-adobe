<?php namespace RamonLeenders\BlockDesigner\FieldType;

defined('C5_EXECUTE') or die("Access Denied.");

class FieldType
{
    protected $ftHandle;
    protected $ftDirectory;

    protected $dbType = false;
    protected $canRepeat = false;
    protected $repeating = false;
    protected $pkgVersionRequired = false;
    protected $appVersionRequired = false;
    protected $requiredSlug = true;
    protected $useBaseFields = true;

    protected $data = [];
    protected $postData = [];
    protected $uses = [];
    protected $helpers = [];

    public function __construct($fieldTypeDirectory, $pkgHandle, $pkgDirectory, $className)
    {
        $this->ftDirectory = $fieldTypeDirectory . DIRECTORY_SEPARATOR;
        $this->pkgHandle = $pkgHandle;
        $this->pkgDirectory = $pkgDirectory;
        $this->className = $className;
    }

    public function getFieldName()
    {
        return t(ucwords(implode(' ', explode('_', $this->ftHandle))));
    }

    public function getFieldDescription()
    {
        return '';
    }

    public function inc($file, $variables = [])
    {
        if (file_exists($file)) {
            ob_start();
            if (is_array($variables) && !empty($variables)) {
                foreach ($variables as $k => $v) {
                    ${$k} = $v;
                }
            }
            include($file);
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
        return;
    }

    public function getTabKey()
    {
        return isset($this->tab) ? $this->tab : false;
    }

    public function view($view, $variables = [], $pkgHandle = null)
    {
        return $this->inc('packages' . DIRECTORY_SEPARATOR . (trim($pkgHandle) == '' ? $this->pkgHandle : $pkgHandle) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . ($this->pkgHandle != 'block_designer' ? 'BlockDesigner' . DIRECTORY_SEPARATOR : null) . 'FieldType' . DIRECTORY_SEPARATOR . $this->className . DIRECTORY_SEPARATOR . $view, $variables);
    }

    public function getPkgVersionRequired()
    {
        return isset($this->pkgVersionRequired) && $this->pkgVersionRequired !== false ? $this->pkgVersionRequired : false;
    }

    public function getAppVersionRequired()
    {
        return isset($this->appVersionRequired) && $this->appVersionRequired !== false ? $this->appVersionRequired : false;
    }

    public function getDbType()
    {
        return $this->dbType;
    }

    public function getHandle()
    {
        return $this->ftHandle;
    }

    public function getRequiredSlug()
    {
        return $this->requiredSlug;
    }

    public function getHelpers()
    {
        return $this->helpers;
    }

    public function getUses()
    {
        return $this->uses;
    }

    public function getCanRepeat()
    {
        return $this->canRepeat;
    }

    public function setRepeating($bool)
    {
        $this->repeating = (bool)$bool;
    }

    public function getRepeating()
    {
        return $this->repeating;
    }

    public function getUseBaseFields()
    {
        return $this->useBaseFields;
    }

    public function setPostData($postData = [])
    {
        $this->postData = $postData;
    }

    public function setData($data = [])
    {
        $this->data = $data;
    }

    public function getFieldTypeJavascript()
    {
        return file_exists($this->ftDirectory . 'elements' . DIRECTORY_SEPARATOR . 'field_javascript.js') ? 'src' . ($this->pkgHandle != 'block_designer' ? '/BlockDesigner' : null) . '/FieldType/' . $this->className . '/elements/field_javascript.js' : false;
    }

    public function getFieldTypeCss()
    {
        return file_exists($this->ftDirectory . 'elements' . DIRECTORY_SEPARATOR . 'field_css.css') ? 'src' . ($this->pkgHandle != 'block_designer' ? '/BlockDesigner' : null) . '/FieldType/' . $this->className . '/elements/field_css.css' : false;
    }

    public function generateFormContent($type, $values = [], $repeatable = false)
    {
        switch ($type) {
            case 'label':
                $labelInner = 't("' . h($values['label']) . '")' . (isset($values['suffix']) ? $values['suffix'] : null);
                $labelInner .= isset($values['description']) && trim($values['description']) != '' ? " . ' " . sprintf('<i class="fa fa-question-circle launch-tooltip" data-original-title="%s"></i>' . "'", '\' . t("' . h($values['description']) . '") . \'') : null;
                if ($repeatable) {
                    return '<label for="<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']" class="control-label"><?php echo ' . $labelInner . '; ?></label>';
                }

                return '<?php echo $form->label($view->field(\'' . $values['slug'] . '\'), ' . $labelInner . '); ?>';
            case 'required':
                if (isset($values['array'])) {
                    return '<?php echo isset(' . $values['array'] . ') && in_array(\'' . $values['slug'] . '\', ' . $values['array'] . ') ? \'<small class="required">\' . t(\'Required\') . \'</small>\' : null; ?>';
                }

                return '<small class="required"><?php echo t(\'Required\'); ?></small>';
            case 'file':
                if ($repeatable) {
                    $baseAttributes = ['id' => $values['parent']['slug'] . '-' . $values['slug'] . '-file-{{id}}',];
                    $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;

                    return '<div ' . join(' ', array_map(function ($key) use ($attributes) {
                            if (is_bool($attributes[$key])) {
                                return $attributes[$key] ? $key : '';
                            }
                            return $key . '="' . $attributes[$key] . '"';
                        }, array_keys($attributes))) . '>
<concrete-file-input file-id="{{ ' . $values['slug'] . ' }}"
                                                     choose-text="<?php echo t(\'Choose File\'); ?>"
                                                     input-name="<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']">
                                </concrete-file-input>
</div>';
                }

                return '<?php echo Core::make("helper/concrete/asset_library")->file(\'' . $values['slug'] . '-\' . $identifier_getString, $view->field(\'' . $values['postName'] . '\'), ' . (isset($values['chooseText']) && trim($values['chooseText']) != '' ? $values['chooseText'] : 't("Choose File")') . ', isset(' . $values['bf'] . ') ? ' . $values['bf'] . ' : null); ?>';
            case 'image':
                if ($repeatable) {
                    $baseAttributes = ['id' => $values['parent']['slug'] . '-' . $values['slug'] . '-image-{{id}}',];
                    $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;

                    return '<div ' . join(' ', array_map(function ($key) use ($attributes) {
                            if (is_bool($attributes[$key])) {
                                return $attributes[$key] ? $key : '';
                            }
                            return $key . '="' . $attributes[$key] . '"';
                        }, array_keys($attributes))) . '>
<concrete-file-input file-id="{{ ' . $values['slug'] . ' }}"
                                                     choose-text="<?php echo t(\'Choose Image\'); ?>"
                                                     input-name="<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']">
                                </concrete-file-input>
</div>';
                }

                return '<?php echo Core::make("helper/concrete/asset_library")->image(\'' . $values['slug'] . '-\' . $identifier_getString, $view->field(\'' . $values['postName'] . '\'), ' . (isset($values['chooseText']) && trim($values['chooseText']) != '' ? $values['chooseText'] : 't("Choose Image")') . ', isset(' . $values['bf'] . ') ? ' . $values['bf'] . ' : null); ?>';
            case 'editor':
                if ($repeatable) {
                    $baseAttributes = [
	                    'name'  => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'id'    => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'class' => 'form-control',
                    ];
                    $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;
                    return '<textarea ' . join(' ', array_map(function ($key) use ($attributes) {
                        if (is_bool($attributes[$key])) {
                            return $attributes[$key] ? $key : '';
                        }
                        return $key . '="' . $attributes[$key] . '"';
                    }, array_keys($attributes))) . '>{{ ' . $values['slug'] . ' }}</textarea>';
                }

                return '<?php echo Core::make(\'editor\')->outputBlockEditModeEditor($view->field(\'' . $values['slug'] . '\'), isset($' . $values['slug'] . ') ? $' . $values['slug'] . ' : null); ?>';
            case 'page_selector':
                if ($repeatable) {
                    $baseAttributes = ['id' => $values['parent']['slug'] . '-' . $values['slug'] . '-page-{{id}}',];
                    $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;

                    return '<div ' . join(' ', array_map(function ($key) use ($attributes) {
                            if (is_bool($attributes[$key])) {
                                return $attributes[$key] ? $key : '';
                            }
                            return $key . '="' . $attributes[$key] . '"';
                        }, array_keys($attributes))) . '>
   <concrete-page-input page-id="{{ ' . $values['slug'] . ' }}" 
                                                        input-name="<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']" 
                                                        choose-text="<?php echo t(\'Choose Page\') ?>" 
                                                        include-system-pages="false" 
                                                        ask-include-system-pages="false">
                                </concrete-page-input>
</div>';
                }

                return '<?php echo Core::make("helper/form/page_selector")->selectPage($view->field(\'' . $values['slug'] . '\'), isset($' . $values['slug'] . ') ? $' . $values['slug'] . ' : null); ?>';
            case 'textarea':
            case 'text':
                if ($repeatable) {
                    $slug = !isset($values['escape']) || $values['escape'] === true ? '{{ ' . $values['slug'] . ' }}' : '{{{ ' . $values['slug'] . ' }}}';
                    $baseAttributes = [
	                    'name'  => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'id'    => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'class' => 'form-control',
                    ];
                    switch ($type) {
                        case 'text':
                            $baseAttributes['type'] = 'text';
                            $baseAttributes['value'] = isset($values['value']) ? $values['value'] : $slug;
                            $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;
                            return '<input ' . join(' ', array_map(function ($key) use ($attributes) {
                                if (is_bool($attributes[$key])) {
                                    return $attributes[$key] ? $key : '';
                                }
                                return $key . '="' . $attributes[$key] . '"';
                            }, array_keys($attributes))) . ' />';
                            break;
                        case 'textarea':
                            $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;
                            return '<textarea ' . join(' ', array_map(function ($key) use ($attributes) {
                                if (is_bool($attributes[$key])) {
                                    return $attributes[$key] ? $key : '';
                                }
                                return $key . '="' . $attributes[$key] . '"';
                            }, array_keys($attributes))) . '>' . $slug . '</textarea>';
                    }
                }

                return '<?php echo $form->' . $type . '($view->field(\'' . $values['slug'] . '\'), ' . (isset($values['value']) ? $values['value'] : 'isset($' . $values['slug'] . ') ? $' . $values['slug'] . ' : null') . ', ' . (isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? var_export($values['attributes'], true) : '[]') . '); ?>';
            case 'select':
                if ($repeatable) {
                    $baseAttributes = [
	                    'name'  => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'id'    => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'class' => 'form-control',
                    ];
                    $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;
                    return '<?php $' . $values['parent']['slug'] . ucfirst($values['slug']) . '_options = ' . $values['options'] . '; ?>
                    <select ' . join(' ', array_map(function ($key) use ($attributes) {
                        if (is_bool($attributes[$key])) {
                            return $attributes[$key] ? $key : '';
                        }
                        return $key . '="' . $attributes[$key] . '"';
                    }, array_keys($attributes))) . '>{{#select ' . $values['slug'] . '}}<?php foreach ($' . $values['parent']['slug'] . ucfirst($values['slug']) . '_options as $k => $v) {
                        echo "<option value=\'" . $k . "\'>" . $v . "</option>";
                     } ?>{{/select}}</select>';
                }

                return '<?php echo $form->' . $type . '($view->field(\'' . $values['slug'] . '\'), ' . $values['options'] . ', isset($' . $values['slug'] . ') ? $' . $values['slug'] . ' : null, ' . (isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? var_export($values['attributes'], true) : '[]') . '); ?>';
            case 'select_multiple':
                if ($repeatable) {
                    $baseAttributes = [
	                    'name'     => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . '][]',
	                    'id'       => '<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']',
	                    'class'    => 'form-control',
	                    'multiple' => 'multiple',
                    ];
                    $attributes = isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? array_merge($baseAttributes, $values['attributes']) : $baseAttributes;
                    return '<?php $' . $values['parent']['slug'] . ucfirst($values['slug']) . '_options = ' . $values['options'] . '; ?>
                    <select ' . join(' ', array_map(function ($key) use ($attributes) {
                        if (is_bool($attributes[$key])) {
                            return $attributes[$key] ? $key : '';
                        }
                        return $key . '="' . $attributes[$key] . '"';
                    }, array_keys($attributes))) . '>{{#select ' . $values['slug'] . '}}<?php foreach ($' . $values['parent']['slug'] . ucfirst($values['slug']) . '_options as $k => $v){
                        echo "<option value=\'" . $k . "\'>" . $v . "</option>";
                     } ?>{{/select}}</select>';
                }

                return '<?php echo $form->selectMultiple($view->field(\'' . $values['slug'] . '\'), ' . $values['options'] . ', ' . (isset($values['defaultValues']) ? $values['defaultValues'] : false) . ', ' . (isset($values['attributes']) && is_array($values['attributes']) && !empty($values['attributes']) ? var_export($values['attributes'], true) : '[]') . '); ?>';
            case 'express_entry_selector':
                if ($repeatable) {
                    return '<div class="express-entry-selector" data-express-entity-id="<?php echo $' . $values['parent']['slug'] . '[\'' . $values['slug'] . '_entity\']->getId(); ?>" data-express-input-name="<?php echo $view->field(\'' . $values['parent']['slug'] . '\'); ?>[{{id}}][' . $values['slug'] . ']" data-express-entry-id="{{ ' . $values['slug'] . ' }}"></div>';
                }

                return '<?php echo Core::make("form/express/entry_selector")->selectEntry($' . $values['slug'] . '_entity, $view->field(\'' . $values['slug'] . '\'), trim($' . $values['slug'] . ') != \'\' ? $entity_Manager->find(\'Concrete\Core\Entity\Express\Entry\', isset($' . $values['slug'] . ') ? $' . $values['slug'] . ' : null) : null); ?>';
        }
    }
} 