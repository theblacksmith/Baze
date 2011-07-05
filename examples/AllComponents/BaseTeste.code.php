<?php

import('system.web.ui.form.Form');
import('system.web.ui.Label');
import('system.web.ui.form.TextBox');
import('system.web.ui.Button');
import('system.web.ui.form.TextArea');
import("system.web.ui.form.CheckBox");
import("system.web.ui.form.RadioButton");
import("system.web.ui.form.DropDownList");
import("system.web.ui.form.OptionItem");
import("system.web.ui.form.ListBox");
import("system.web.ui.form.PasswordField");
import("system.web.ui.form.FormImage");
import("system.web.ui.form.Reset");
import("system.web.ui.page.Page");


class BaseTeste extends Page
{
	/**
	 * @var Form
	 */
	public $frm;

	/**
	 * @var Label
	 */
	public $label_1;

	/**
	 * @var TextBox
	 */
	public $textbox_1;

	/**
	 * @var Button
	 */
	public $buttonServidor_1;

	/**
	 * @var Button
	 */
	public $buttonServidor_1_reset;
	
	/**
	 * @var Button
	 */
	public $buttonCliente_1;
	
	/**
	 * @var Button
	 */
	public $buttonCliente_1_reset;
	
	

	/**
	 * @var Label
	 */
	public $label_2;

	/**
	 * @var TextArea
	 */
	public $textarea_2;

	/**
	 * @var Button
	 */
	public $buttonServidor_2;
	
	/**
	 * @var Button
	 */
	public $buttonServidor_2_reset;

	/**
	 * @var Button
	 */
	public $buttonCliente_2;
	
	

	/**
	 * @var CheckBox
	 */
	public $checkbox_3_1;

	/**
	 * @var Label
	 */
	public $label_3_1;

	/**
	 * @var CheckBox
	 */
	public $checkbox_3_2;

	/**
	 * @var Label
	 */
	public $label_3_2;

	/**
	 * @var Button
	 */
	public $buttonServidor_3_1;
	
	/**
	 * @var Button
	 */
	public $buttonServidor_3_2;
	

	/**
	 * @var Button
	 */
	public $buttonCliente_3_1;
	
	/**
	 * @var Button
	 */
	public $buttonCliente_3_2;
	

	/**
	 * @var RadioButton
	 */
	public $radiobutton_4_1;

	/**
	 * @var Label
	 */
	public $label_4_1;

	/**
	 * @var RadioButton
	 */
	public $radiobutton_4_2;

	/**
	 * @var Label
	 */
	public $label_4_2;

	/**
	 * @var Button
	 */
	public $buttonServidor_4;

	/**
	 * @var Button
	 */
	public $buttonCliente_4;

	
	/**
	 * @var Label
	 */
	public $label_5_1;

	/**
	 * @var DropDownList
	 */
	public $dropDownList_5;

	/**
	 * @var OptionItem
	 */
	public $opt_for_dropdown_1;

	/**
	 * @var Label
	 */
	public $label_5_2;

	/**
	 * @var TextBox
	 */
	public $textbox_5;

	/**
	 * @var Button
	 */
	public $buttonServidor_5_Add;

	/**
	 * @var Button
	 */
	public $buttonServidor_5_Del;

	/**
	 * @var Button
	 */
	public $buttonServidor_5_Chg;

	/**
	 * @var Button
	 */
	public $buttonCliente_5_Add;

	/**
	 * @var Button
	 */
	public $buttonCliente_5_Del;

	/**
	 * @var Button
	 */
	public $buttonCliente_5_Chg;

	/**
	 * @var Label
	 */
	public $label_6_1;

	/**
	 * @var ListBox
	 */
	public $listBox_6;

	/**
	 * @var OptionItem
	 */
	public $opt_for_listbox_unic_1;

	/**
	 * @var Label
	 */
	public $label_6_2;

	/**
	 * @var TextBox
	 */
	public $textbox_6;

	/**
	 * @var Button
	 */
	public $buttonServidor_6_Add;

	/**
	 * @var Button
	 */
	public $buttonServidor_6_Del;

	/**
	 * @var Button
	 */
	public $buttonServidor_6_Chg;

	/**
	 * @var Button
	 */
	public $buttonCliente_6_Add;

	/**
	 * @var Button
	 */
	public $buttonCliente_6_Del;

	/**
	 * @var Button
	 */
	public $buttonCliente_6_Chg;

	/**
	 * @var Label
	 */
	public $label_7_1;

	/**
	 * @var ListBox
	 */
	public $listBox_7;

	/**
	 * @var OptionItem
	 */
	public $opt_for_listbox_mult_1;

	/**
	 * @var Label
	 */
	public $label_7_2;

	/**
	 * @var TextBox
	 */
	public $textbox_7;

	/**
	 * @var Button
	 */
	public $buttonServidor_7_Add;

	/**
	 * @var Button
	 */
	public $buttonServidor_7_Del;

	/**
	 * @var Button
	 */
	public $buttonServidor_7_Chg;

	/**
	 * @var Button
	 */
	public $buttonCliente_7_Add;

	/**
	 * @var Button
	 */
	public $buttonCliente_7_Del;

	/**
	 * @var Button
	 */
	public $buttonCliente_7_Chg;

	/**
	 * @var Label
	 */
	public $label_8;

	/**
	 * @var PasswordField
	 */
	public $passwordfield_8;

	/**
	 * @var Button
	 */
	public $buttonServidor_8;

	/**
	 * @var Button
	 */
	public $buttonCliente_8;
	
	/**
	 * @var Button
	 */
	public $buttonServidor_8_reset;
	
	/**
	 * @var FormImage
	 */
	public $formImageSubmit;

	/**
	 * @var Reset
	 */
	public $inputReset;

	public function Page_Init()
	{
		$this->addEvents();
		$this->listBox_7->set('multiple',true);
	}
	
	public function addEvents()
	{
		//TextBox
		$this->buttonServidor_1->addEventListener(CLICK, array($this,'changeLabelTextbox'));
		$this->buttonServidor_1_reset->addEventListener(CLICK, array($this,'resetTextbox'));
		
		//TextArea
		$this->buttonServidor_2->addEventListener(CLICK, array($this,'changeLabelTextarea'));
		$this->buttonServidor_2_reset->addEventListener(CLICK, array($this,'resetTextarea'));
		
		//CheckBox
		$this->checkbox_3_1->addEventListener(CHANGE, array($this,'changeLabelCheck'));
		$this->checkbox_3_2->addEventListener(CHANGE, array($this,'changeLabelCheck'));
		$this->buttonServidor_3_1->addEventListener(CLICK, array($this,'checkAll'));
		$this->buttonServidor_3_2->addEventListener(CLICK, array($this,'uncheckAll'));
		
		//RadioButton
		$this->radiobutton_4_1->addEventListener(CHANGE, array($this,'changeLabelRadio'));
		$this->radiobutton_4_2->addEventListener(CHANGE, array($this,'changeLabelRadio'));
		$this->buttonServidor_4->addEventListener(CLICK, array($this,'switchRadio'));
		
		//DropDownList
		$this->dropDownList_5->addEventListener(CHANGE, array($this,'displayDropDownItemSelected'));
		$this->buttonServidor_5_Add->addEventListener(CLICK, array($this,'addItemToDropDown'));
		$this->buttonServidor_5_Chg->addEventListener(CLICK, array($this,'changeItemFromDropDown'));
		$this->buttonServidor_5_Del->addEventListener(CLICK, array($this,'removeItemFromDropDown'));
		
		//ListBox (seleção única)
		$this->listBox_6->addEventListener(CHANGE, array($this,'displayListBoxItemSelected'));
		$this->buttonServidor_6_Add->addEventListener(CLICK, array($this,'addItemToListBox'));
		$this->buttonServidor_6_Chg->addEventListener(CLICK, array($this,'changeItemFromListBox'));
		$this->buttonServidor_6_Del->addEventListener(CLICK, array($this,'removeItemFromListBox'));
		
		//ListBox (seleção múltipla)
		$this->listBox_7->addEventListener(CHANGE, array($this,'displayListBoxItemSelecteds'));
		$this->buttonServidor_7_Add->addEventListener(CLICK, array($this,'addItemToListBoxMultiple'));
		$this->buttonServidor_7_Chg->addEventListener(CLICK, array($this,'changeItemFromListBoxMultiple'));
		$this->buttonServidor_7_Del->addEventListener(CLICK, array($this,'changeItemFromListBoxMultiple'));
		
		//PassowrdField
		$this->buttonServidor_8->addEventListener(CLICK, array($this,'changeLabelPass'));
		$this->buttonServidor_8_reset->addEventListener(CLICK, array($this,'resetPass'));
		
		//Form
		$this->frm->addEventListener(SUBMIT, array($this,'submitForm'),true,true);
	}
	
	//1 - TextBox
	public function changeLabelTextbox(Component $sender, $args)
	{
		$this->label_1->setText($this->textbox_1->get('value'));
	}
	public function resetTextbox(Component $sender, $args)
	{
		$this->label_1->setText('Label para TextBox:');
		$this->textbox_1->set('value','some value');
	}

	
	//2 - TextArea
	public function changeLabelTextarea(Component $sender, $args)
	{
		$this->label_2->setText($this->textarea_2->get('value'));
	}
	public function resetTextarea(Component $sender, $args)
	{
		$this->label_2->setText('Label para TextArea:');
		$this->textarea_2->set('value','some value');
	}
	
	
	//3 - CheckBox
	public function changeLabelCheck(Component $sender, $args)
	{
		$label = 'label' . substr($sender->get('id'),8);
		
		if($sender->isChecked() == true || $sender->get('checked') == 'checked')
		{
			$this->$label->set('text','checked');
		}
		else
		{
			$this->$label->set('text','unchecked');
		}
	}
	public function checkAll(Component $sender, $args)
	{
		$this->checkbox_3_1->set('checked',true);
		$this->label_3_1->set('text','check');
		
		$this->checkbox_3_2->set('checked',true);
		$this->label_3_2->set('text','check');
	}
	public function uncheckAll(Component $sender, $args)
	{
		$this->checkbox_3_1->set('checked',false);
		$this->label_3_1->set('text','unchecked');
		
		$this->checkbox_3_2->set('checked',false);
		$this->label_3_2->set('text','unchecked');
	}
	
	
	//4 - RadioButton
	public function changeLabelRadio(Component $sender, $args)
	{
		if($this->radiobutton_4_1->get('checked') == true || $this->radiobutton_4_1->get('checked') == 'checked')
		{
			$this->label_4_1->setText('checked');
			$this->label_4_2->setText('unchecked');
		}
		
		if($this->radiobutton_4_2->get('checked') == true || $this->radiobutton_4_2->get('checked') == 'checked')
		{
			$this->label_4_1->setText('unchecked');
			$this->label_4_2->setText('checked');
		}
	}
	public function switchRadio(Component $sender, $args)
	{
		if( ($this->radiobutton_4_1->get('checked') == true || $this->radiobutton_4_1->get('checked') == 'checked'))
		{
			$this->label_4_1->setText('unchecked');
			
			$this->label_4_2->setText('checked');
			$this->radiobutton_4_2->check();
		}
	}
	
	//5 - DropDownList
	public function displayDropDownItemSelected(Component $sender, $args)
	{		
		$this->textbox_5->set('value',$this->dropDownList_5->getSelectedItem()->getText());
	}
	public function addItemToDropDown(Component $sender, $args)
	{
		$newOptionItem = new OptionItem();
		$newOptionItem->set('id','OPTION_ITEM_FOR_DDL_' . time());
		$newOptionItem->set('runat','server');
		$newOptionItem->setText($this->textbox_5->get('value'));
		$newOptionItem->setValue('VALUE_' . $this->textbox_5->get('value'));
				
		$this->dropDownList_5->addChild($newOptionItem);
		$newOptionItem->setSelected(true);
		
		$this->textbox_5->set('value','');
	}
	public function changeItemFromDropDown(Component $sender, $args)
	{
		if( count($this->dropDownList_5->getChildren()) > 0 )
		{
			$this->dropDownList_5->getSelectedItem()->set('text', $this->textbox_5->get('value'));
			$this->textbox_5->set('value','');
		}
	}
	public function removeItemFromDropDown(Component $sender, $args)
	{
		if( count($this->dropDownList_5->getChildren()) > 0 )
		{
			$this->dropDownList_5->removeChild($this->dropDownList_5->getSelectedItem());
			
			if(count($this->dropDownList_5->getChildren()) > 0)
			{
				$children = $this->dropDownList_5->getChildren();
				$children[0]->setSelected();
			}
		}
	}
	
	
	//6 - ListBox (Seleção única)
	public function displayListBoxItemSelected(Component $sender, $args)
	{
		$this->textbox_6->set('value',$this->listBox_6->getSelectedItem()->getText());
	}
	public function addItemToListBox(Component $sender, $args)
	{
		$newOptionItem = new OptionItem();
		$newOptionItem->set('id','OPTION_ITEM_FOR_LISTBOX_UNIC_' . time());
		$newOptionItem->set('runat','server');
		$newOptionItem->setText($this->textbox_6->get('value'));
		$newOptionItem->setValue('VALUE_' . $this->textbox_6->get('value'));
				
		$this->listBox_6->addChild($newOptionItem);
		$newOptionItem->setSelected(true);
		
		$this->textbox_6->set('value','');
	}
	public function changeItemFromListBox(Component $sender, $args)
	{
		if( count($this->listBox_6->getChildren()) > 0 )
		{
			$this->listBox_6->getSelectedItem()->set('text', $this->textbox_6->get('value'));
			$this->textbox_6->set('value',''); 
		}
	}
	public function removeItemFromListBox(Component $sender, $args)
	{
		if( count($this->listBox_6->getChildren()) > 0 )
		{
			$this->listBox_6->removeChild($this->listBox_6->getSelectedItem());
			
			if(count($this->listBox_6->getChildren()) > 0)
			{
				$children = $this->listBox_6->getChildren();
				$children[0]->setSelected();
			}
		}
	}
	
	//7 - ListBox (Seleção múltipla)
	public function displayListBoxItemSelecteds(Component $sender, $args)
	{
		$selectedItems = $this->listBox_7->getSelectedItem();
		
		$text = '';
		
		foreach($selectedItems as $selectedItem)
		{
			$text.= $selectedItem->get('value') . ' ';
		}
		
		$this->textbox_7->set('value',$text);
	}
	
	public function addItemToListBoxMultiple(Component $sender, $args)
	{
		$newOptionItem = new OptionItem();
		$newOptionItem->set('id','OPTION_ITEM_FOR_LISTBOX_MULT_' . time());
		$newOptionItem->set('runat','server');
		$newOptionItem->setText($this->textbox_7->get('value'));
		$newOptionItem->setValue('VALUE_' . $this->textbox_7->get('value'));
				
		$this->listBox_7->addChild($newOptionItem);
		$newOptionItem->setSelected(true);
		
		$this->textbox_7->set('value','');
	}
	
	public function changeItemFromListBoxMultiple(Component $sender, $args)
	{
		if( count($this->listBox_7->getChildren()) > 0 )
		{
			$selectedItems = $this->listBox_7->getSelectedItem();
			
			foreach($selectedItems as $selectedItem)
			{
				$selectedItem->set('text', $this->textbox_7->get('value'));
			}
			
			$this->textbox_7->set('value',''); 
		}
	}
	
	public function removeItemFromListBoxMultiple(Component $sender, $args)
	{
		if( count($this->listBox_7->getChildren()) > 0 )
		{
			$selectedItems = $this->listBox_7->getSelectedItem();
			
			foreach($selectedItems as $selectedItem)
			{
				$this->listBox_7->removeChild($selectedItem);				
			}			
		}
	}
	
	//8 - PasswordField
	public function changeLabelPass(Component $sender, $args)
	{
		$this->label_8->set('text',$this->passwordfield_8->get('value'));
	}
	public function resetPass(Component $sender, $args)
	{
		$this->passwordfield_8->set('value','');
		$this->label_8->set('text','PasswordField:');
	}
	
	//Form
	public function submitForm(Component $sender, $args)
	{
		//bimbauê
	}
}	
