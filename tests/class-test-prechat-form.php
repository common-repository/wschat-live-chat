<?php

use WSChat\PreChatForm\Fields\FormField;
use WSChat\PreChatForm\Fields\FormFieldOption;
use WSChat\PreChatForm\PreChatForm;
use WSChat\PreChatForm\Settings;

class Test_PreChatFormSettings extends WP_UnitTestCase {

	public function test_save_form() {
		$settings = new Settings();

		$form = $settings->get_form();

		$this->assertFalse($form->enabled());

		$form->enable();
		$this->assertTrue($form->enabled());

		$label = 'Test label';
		$form->label($label);

		$form->mode(PreChatForm::MODE_OFFLINE);

		$field1 = new FormField(array(
			'type' => FormField::TYPE_TEXT,
			'deletable' => false,
			'name' => 'Email',
			'placeholder' => 'Place ypur primary email',
			'status' => FormField::STATUS_ACTIVE,
		));

		$field1->deactivate();

		$field2 = FormField::build(array(
			'type' => FormField::TYPE_CHECKBOX,
			'deletable' => true,
			'name' => 'Checkbox',
			'status' => FormField::STATUS_ACTIVE,
			'options' => array(
				array(
					'value' => 'val',
					'label' => 'lab'
				)
			)
		));

		$option = FormFieldOption::build('Option 1', 'Lable 1 ');
		$field2->add_option($option);

		$form->add_field($field1);
		$form->add_field($field2);

		$settings->save_form($form);

		/* Creating a new form */
		$settings = new Settings();

		$form = $settings->get_form();

		$this->assertTrue($form->enabled());
		$this->assertEquals($form->label, $label);
		$this->assertEquals($form->mode, PreChatForm::MODE_OFFLINE);

		$this->assertEquals(count($form->fields), 2);

		$this->assertTrue($form->fields[0]->deactive());

		$this->assertTrue($form->fields[1]->active());

		$this->assertTrue(count($form->fields[1]->options) === 2);
		$this->assertTrue($form->fields[1]->has_option($option));
	}
}
