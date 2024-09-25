<?php namespace Concrete\Package\BlockDesignerPro\Src\BlockDesigner\FieldType\MultipleSelectFieldType;

defined('C5_EXECUTE') or die("Access Denied.");

use RamonLeenders\BlockDesigner\FieldType\FieldType;
use Zend\Code\Generator\ValueGenerator;
use Config;

class MultipleSelectFieldType extends FieldType
{
	protected $ftHandle = 'multiple_select';
	protected $uses = ['Database'];
	protected $canRepeat = false;
	protected $pkgVersionRequired = '2.9.1';

	public function getFieldDescription()
	{
		return t("A Multiple Select field");
	}

	public function getSearchableContent()
	{
		$optionsArray = $this->buildOptions();
		$viewOutput = isset($this->data['view_output']) && trim($this->data['view_output']) != '' && in_array($this->data['view_output'], [0, 1, 2, 3, 4]) ? $this->data['view_output'] : 0;
		return '$' . $this->data['slug'] . '_options = [' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $optionsArray) . PHP_EOL . '    ' . '    ];
        foreach($this->getMultipleSelectSelections(\'' . $this->getEntriesTableName() . '\', $this->bID, $' . $this->data['slug'] . '_options) as $' . $this->data['slug'] . '_key => $' . $this->data['slug'] . '_value){
            $content[] = ' . (in_array($viewOutput, [0, 2]) ? '$' . $this->data['slug'] . '_value' : '$' . $this->data['slug'] . '_key') . ';
        }';
	}

	private function _options()
	{
		$options = [];
		if (isset($this->data['select_options']) && trim($this->data['select_options']) != '') {
			$options_exploded = explode("\n", $this->data['select_options']);
			$max_key = 0;
			foreach ($options_exploded as $option_exploded) {
				list($before, $after) = explode(' :: ', $option_exploded, 2);
				if (trim($after) != '') {
					$key = strip_tags($before);
					$key_no = 0;
					while (array_key_exists($key, $options)) {
						$key_no++;
						$key = $before . '_' . $key_no;
					}
					if (is_numeric($key) && $key > $max_key) {
						$max_key = $key;
					}
					$options[$key] = h(trim($after));
				} else {
					$max_key++;
					$options[$max_key] = h(trim($before));
				}
			}
		}
		return $options;
	}

	private function buildOptions()
	{
		$options = $this->_options();
		$optionsArray = [];
		$translate = isset($this->data['translate']) && $this->data['translate'] == '1' ? true : false;
		foreach ($options as $key => $option) {
			$key = trim($key) == '' ? "''" : "'$key'";
			if ($translate) {
				$optionsArray[] = '        ' . $key . ' => t("' . trim($option) . '")';
			} else {
				$optionsArray[] = '        ' . $key . ' => "' . trim($option) . '"';
			}
		}
		return $optionsArray;
	}

	public function getViewContents()
	{
		$slug = $this->getRepeating() ? $this->data['parent']['slug'] . '_item["' . $this->data['slug'] . '"]' : $this->data['slug'];
		$viewOutput = isset($this->data['view_output']) && trim($this->data['view_output']) != '' && in_array($this->data['view_output'], [0, 1, 2, 3, 4]) ? $this->data['view_output'] : 0;
		if (in_array($viewOutput, [2, 3])) {
			$inner = '<?php foreach($' . $slug . ' as $' . $slug . '_key => $' . $slug . '_value){ ?>' . (isset($this->data['prefix_option']) ? $this->data['prefix_option'] : null);
			if ($viewOutput == 2) {
				$inner .= '<?php echo $' . $slug . '_value; ?>';
			} else {
				$inner .= '<?php echo $' . $slug . '_key; ?>';
			}
			$inner .= (isset($this->data['suffix_option']) ? $this->data['suffix_option'] : null) . '<?php } ?>';
		} else {
			if ($viewOutput == 1) {
				$inner = '<?php echo implode(\', \', array_keys($' . $slug . ')); ?>';
			} else {
				$inner = '<?php echo implode(\', \', $' . $slug . '); ?>';
			}
		}
		return '<?php if (!empty($' . $slug . ')) { ?>' . $this->data['prefix'] . $inner . $this->data['suffix'] . '<?php } ?>';
	}

	public function copyFiles()
	{
		$files = [];
		if ($this->data['ft_count'] <= 0) {
			$files[] = [
				'source' => $this->ftDirectory . 'js' . DIRECTORY_SEPARATOR,
				'target' => $this->data['btDirectory'] . 'js_form' . DIRECTORY_SEPARATOR,
			];
		}
		return $files;
	}

	private function getEntriesTableName()
	{
		return $this->data['btTable'] . ucfirst($this->data['slug']) . '_MultipleSelectEntries';
	}

	public function getViewFunctionContents()
	{
		$optionsArray = $this->buildOptions();
		return '$' . $this->data['slug'] . '_options = [' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $optionsArray) . PHP_EOL . '    ' . '    ];
        $this->set("' . $this->data['slug'] . '_options", $' . $this->data['slug'] . '_options);
        $this->set("' . $this->data['slug'] . '", $this->getMultipleSelectSelections(\'' . $this->getEntriesTableName() . '\', $this->bID, $' . $this->data['slug'] . '_options));';
	}

	protected function getMultipleSelectClassName(){
		return 'ft-multipleSelect-' . $this->data['block_handle'] . '-' . $this->data['slug'];
	}

	public function getFormContents()
	{
		$repeating = $this->getRepeating();
		$btFieldsRequired = $repeating ? '$btFieldsRequired[\'' . $this->data['parent']['slug'] . '\']' : '$btFieldsRequired';
		$slug = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '\']' : $this->data['slug'];
		$slugOptions = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\']' : $this->data['slug'] . '_options';
		$options = $this->chosenForSortable() ? '$' . $slug . ' + $' . $slugOptions : '$' . $slugOptions;
		$selectAttributes = [
			'class' => $this->getMultipleSelectClassName(),
		];
		$code = null;
		$code .= '<div class="form-group">
    ' . parent::generateFormContent('label', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'label' => $this->data['label'], 'description' => $this->data['description']], $repeating) . '
    ' . parent::generateFormContent('required', ['slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'array' => $btFieldsRequired], $repeating) . '
    ' . parent::generateFormContent('select_multiple', ['attributes' => $selectAttributes, 'slug' => $this->data['slug'], 'parent' => isset($this->data['parent']) ? $this->data['parent'] : null, 'options' => $options, 'defaultValues' => 'array_keys($' . $slug . ')'], $repeating);
		if($this->chosenForSortable()){
			$code .= '
	<small><?php echo t("Drag/drop to sort the items"); ?></small>';
		}
		$code .= '
</div>';
		$code .= PHP_EOL . PHP_EOL . '<script type="text/javascript">
    Concrete.event.publish(\'' . $this->data['block_handle'] . '.' . $this->data['slug'] . '.multiple_select\');
</script>';
		return $code;
	}

	public function getSaveFunctionContents()
	{
		$repeating = $this->getRepeating();
		$slugTable = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_table\']' : $this->data['slug'] . '_table';
		$slugEntries = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_entries\']' : $this->data['slug'] . '_entries';
		$slugSortOrder = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_sortOrder\']' : $this->data['slug'] . '_sortOrder';
		$slugEntry = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_entry\']' : $this->data['slug'] . '_entry';
		$slugEntryID = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_entryID\']' : $this->data['slug'] . '_entryID';
		$slugData = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_data\']' : $this->data['slug'] . '_data';
		$slugValue = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_value\']' : $this->data['slug'] . '_value';
		return '$' . $slugTable . ' = \'' . $this->getEntriesTableName() . '\';
		$' . $slugEntries . ' = $this->getMultipleSelectSelections($' . $slugTable . ', $this->bID, [], true);
		if (isset($args[\'' . $this->data['slug'] . '\']) && is_array($args[\'' . $this->data['slug'] . '\'])) {
			$' . $slugSortOrder . ' = 1;
			foreach ($args[\'' . $this->data['slug'] . '\'] as $' . $slugValue . ') {
				$' . $slugData . ' = [
					\'value\'     => $' . $slugValue . ',
					\'sortOrder\' => $' . $slugSortOrder . ',
					\'bvID\'      => $this->bID,
				];
				if (!empty($' . $slugEntries . ')) {
					$' . $slugEntryID . ' = key($' . $slugEntries . ');
					$db->update($' . $slugTable . ', $' . $slugData . ', [\'msID\' => $' . $slugEntryID . ']);
					unset($' . $slugEntries . '[$' . $slugEntryID . ']);
				} else {
					$db->insert($' . $slugTable . ', $' . $slugData . ');
				}
				$' . $slugSortOrder . '++;
			}
		}
		if (!empty($' . $slugEntries . ')) {
			foreach (array_keys($' . $slugEntries . ') as $' . $slugEntry . ') {
				$db->delete($' . $slugTable . ', [\'msID\' => $' . $slugEntry . ']);
			}
		}';
	}

	protected function varExportArray($array, $arrayStrip = false)
	{
		$valueGenerator = version_compare(Config::get('concrete.version'), '8.0.0', '>=');
		$export = $valueGenerator ? (new ValueGenerator($array, ValueGenerator::TYPE_ARRAY_SHORT))->setIndentation('  ')->generate() : var_export($array, true);
		if ($arrayStrip) {
			$arrayEndString = $valueGenerator ? '\]' : '\)';
			$arrayEndStringReplace = $valueGenerator ? ']' : ')';
			$patterns = ['(\d+\s=>)', "/\s+/", "/\s([?.!])/", '/,' . $arrayEndString . '/', '/\',\'/', '/=>/', '/' . $arrayEndString . ',\'/'];
			$replacer = ['', '', '$1', $arrayEndStringReplace, "', '", ' => ', $arrayEndStringReplace . ', \''];
			$export = preg_replace($patterns, $replacer, $export);
		}
		return $export;
	}

	public function getAddFunctionContents()
	{
		$repeating = $this->getRepeating();
		$slug = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '\']' : $this->data['slug'];
		$slugDefaults = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_defaults\']' : $this->data['slug'] . '_defaults';
		$slugDefault = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_default\']' : $this->data['slug'] . '_default';
		$slugOptions = $repeating ? $this->data['parent']['slug'] . '[\'' . $this->data['slug'] . '_options\']' : $this->data['slug'] . '_options';
		$options = $this->_options();
		$defaultValuesArray = [];
		if (isset($this->data['default_values']) && trim($this->data['default_values']) != '') {
			$defaultValues = array_filter(explode(',', $this->data['default_values']));
			foreach ($defaultValues as $defaultValue) {
				$defaultValue = h(trim($defaultValue));
				if (isset($options[$defaultValue])) {
					$defaultValuesArray[] = $defaultValue;
				}
			}
		}
		return '$' . $slug . ' = [];
		$' . $slugDefaults . ' = array_unique(' . $this->varExportArray($defaultValuesArray, true) . ');
		$' . $slugOptions . ' = $this->get("' . $slugOptions . '");
		if (!empty($' . $slugDefaults . ')) {
			foreach ($' . $slugDefaults . ' as $' . $slugDefault . ') {
				if (isset($' . $slugOptions . '[$' . $slugDefault . '])) {
					$' . $slug . '[$' . $slugDefault . '] = $' . $slugOptions . '[$' . $slugDefault . '];
				}
			}
		}
		$this->set("' . $slug . '", $' . $slug . ');';
	}

	public function getEditFunctionContents()
	{
		return '$this->set("' . $this->data['slug'] . '", $this->getMultipleSelectSelections(\'' . $this->getEntriesTableName() . '\', $this->bID, $this->get("' . $this->data['slug'] . '_options")));';
	}

	public function getAddEditFunctionContents()
	{
		$optionsArray = $this->buildOptions();
		return '$this->set("' . $this->data['slug'] . '_options", [' . PHP_EOL . '    ' . implode(',' . PHP_EOL . '    ', $optionsArray) . PHP_EOL . '    ' . '    ]);';
	}

	public function getAutoCssContents()
	{
		if ($this->data['ft_count'] <= 0) {
			return '.select2-container.form-control{border:0;padding:0;height:auto;}';
		}
	}

	public function getAutoJsContents()
	{
		if ($this->chosenForSortable()) {
			$inner = '        $(\'.' . $this->getMultipleSelectClassName() . '\').select2_sortable();
        $(\'.' . $this->getMultipleSelectClassName() . '\').on("select2-selecting", function (evt) {
            var $element = $(evt.object.element);
            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });';
		} else {
			$inner = '        $(\'.' . $this->getMultipleSelectClassName() . '\').select2();';
		}
		return 'Concrete.event.bind(\'' . $this->data['block_handle'] . '.' . $this->data['slug'] . '.multiple_select\', function () {
    $(document).ready(function () {
' . $inner . '
    });
});';
	}

	public function getDeleteFunctionContents()
	{
		return '$db->delete(\'' . $this->getEntriesTableName() . '\', [\'bvID\' => $this->bID]);';
	}

	public function getExtraFunctionsContents()
	{
		if ($this->data['ft_count'] <= 0) {
			return 'protected function getMultipleSelectSelections($table, $bvID, $options = [], $save = false)
	{
		$return = [];
		if (trim($bvID) != \'\' && $bvID > 0 && ($items = Database::connection()->fetchAll(\'SELECT * FROM \' . $table . \' WHERE bvID = ? ORDER BY sortOrder\', [$bvID]))) {
			foreach ($items as $item) {
				if ($save) {
					$return[$item[\'msID\']] = $item[\'value\'];
				} elseif (isset($options[$item[\'value\']])) {
					$return[$item[\'value\']] = $options[$item[\'value\']];
				}
			}
		}
		return $return;
	}';
		}
	}

	private function chosenForSortable()
	{
		return isset($this->data['sortable']) && is_string($this->data['sortable']) && $this->data['sortable'] == '1';
	}

	public function getFieldOptions()
	{
		return parent::view('field_options.php');
	}

	public function getAssets()
	{
		if ($this->chosenForSortable()) {
			return [
				'addEdit' => [
					'register' => [
						[
							'type'     => 'javascript',
							'handle'   => 'select2sortable',
							'filename' => 'blocks/{{blockHandle}}/js_form/select2.sortable.js',
						],
					],
					'require'  => [
						[
							'type'   => 'css',
							'handle' => 'select2',
						],
						[
							'type'   => 'javascript',
							'handle' => 'select2',
						],
						[
							'type'   => 'javascript',
							'handle' => 'select2sortable',
						],
					],
				],
			];
		} else {
			return [
				'addEdit' => [
					'require' => [
						[
							'type'   => 'css',
							'handle' => 'select2',
						],
						[
							'type'   => 'javascript',
							'handle' => 'select2',
						],
					],
				],
			];
		}
	}

	public function getDbTables()
	{
		$fields = [
			[
				'name'       => 'msID',
				'type'       => 'I',
				'attributes' => [
					'key'           => true,
					'unsigned'      => true,
					'autoincrement' => true,
				]
			],
			[
				'name' => 'bvID',
				'type' => 'I',
			],
			[
				'name' => 'value',
				'type' => 'C',
			],
			[
				'name' => 'sortOrder',
				'type' => 'I',
			],
		];
		return [
			$this->getEntriesTableName() => [
				'fields' => $fields,
			]
		];
	}

	public function getDuplicateFunctionContents()
	{
		return '$' . $this->data['slug'] . '_entries = $db->fetchAll(\'SELECT * FROM ' . $this->getEntriesTableName() . ' WHERE bvID = ? ORDER BY sortOrder ASC\', [$this->bID]);
        foreach ($' . $this->data['slug'] . '_entries as $' . $this->data['slug'] . '_entry) {
            unset($' . $this->data['slug'] . '_entry[\'msID\']);
            $db->insert(\'' . $this->getEntriesTableName() . '\', $' . $this->data['slug'] . '_entry);
        }';
	}

	public function getBtExportTables()
	{
		return [
			$this->getEntriesTableName()
		];
	}

	public function validate()
	{
		$options = $this->_options();
		return !empty($options) ? true : t('No select choices were entered for row #%s.', $this->data['row_id']);
	}
}