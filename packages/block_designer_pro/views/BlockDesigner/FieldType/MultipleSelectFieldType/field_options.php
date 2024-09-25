<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div class="content-field-options">
	<div class="form-group">
		<label for="fields[{{id}}][select_options]" class="control-label">
			<?php echo t('Select choices (one per line)'); ?>
			<span class="required">*</span>

			<small><?php echo t("Set your own array key for values, by using 2 colons (' :: ') on each line - extra spaces required"); ?></small>
		</label>

		<div class="alert alert-info">
			<b>concrete5_old</b> :: Concrete5 CMS 5.6<br/>
			<b>concrete5</b> :: Concrete5 CMS 5.7<br/>
			<b>wordpress</b> :: WordPress<br/>
			<?php echo t('Value without a key, this will be assigned by the field type'); ?>
		</div>

		<textarea
			rows="3"
			data-validation="required"
			name="fields[{{id}}][select_options]"
			id="fields[{{id}}][select_options]"
			class="form-control">{{select_options}}</textarea>
	</div>

	<div class="form-group">
		<label for="fields[{{id}}][translate]" class="control-label">
			<input type="checkbox" name="fields[{{id}}][translate]" value="1" id="fields[{{id}}][translate]" {{#xif " this.translate == '1' " }}checked="checked"{{/xif}}>
			<?php echo t("Use concrete5's translate function for all select choices (values)"); ?>
		</label>
	</div>

	<div class="form-group">
		<label for="fields[{{id}}][sortable]" class="control-label">
			<input type="checkbox" name="fields[{{id}}][sortable]" value="1" id="fields[{{id}}][sortable]" {{#xif " this.sortable == '1' " }}checked="checked"{{/xif}}>
			<?php echo t("User can sort (drag) selections into the wanted order"); ?>
		</label>
	</div>

	<div class="form-group">
		<label for="fields[{{id}}][view_output]" class="control-label">
			<?php echo t('View output'); ?>
		</label>

		<select name="fields[{{id}}][view_output]" class="form-control view_output" id="fields[{{id}}][view_output]">
			{{#select view_output}}
			<option value="0"><?php echo t('Echo the selected values, separated by a comma, i.e. %s', implode(', ', ['Concrete5 CMS 5.6', 'Concrete5 CMS 5.7'])); ?></option>
			<option value="1"><?php echo t('Echo the selected keys, separated by a comma, i.e. %s', implode(', ', ['concrete5_old', 'concrete5'])); ?></option>
			<option value="2"><?php echo t('Echo the selected values, with custom HTML open/HTML close for each item'); ?></option>
			<option value="3"><?php echo t('Echo the selected keys, with custom HTML open/HTML close for each item'); ?></option>
			{{/select}}
		</select>
	</div>

	<div class="form-group-fake multiple-select-html-open-close" style="display: none;">
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label for="fields[{{id}}][prefix_option]" class="control-label">
						<?php echo t("Select choice HTML open"); ?>

						<small>
							<?php echo t('i.e. %s', h('<div class="abc">')); ?>
						</small>
					</label>

					<textarea
						rows="3"
						name="fields[{{id}}][prefix_option]"
						id="fields[{{id}}][prefix_option]"
						class="form-control">{{prefix_option}}</textarea>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="form-group">
					<label for="fields[{{id}}][suffix_option]" class="control-label">
						<?php echo t("Select choice HTML close"); ?>

						<small>
							<?php echo t('i.e. %s', h('</div>')); ?>
						</small>
					</label>

					<textarea
						rows="3"
						name="fields[{{id}}][suffix_option]"
						id="fields[{{id}}][suffix_option]"
						class="form-control">{{suffix_option}}</textarea>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="fields[{{id}}][default_values]" class="control-label">
			<?php echo t('Default Value(s)'); ?>

			<small>
				<?php echo t("Enter the array keys in this field, in case you want to set default values for this field (only for newly added blocks/items). Separate with a comma (,)."); ?>
			</small>
		</label>

		<input type="text"
		       name="fields[{{id}}][default_values]"
		       id="fields[{{id}}][default_values]"
		       value="{{default_values}}"
		       class="form-control"/>
	</div>
</div>