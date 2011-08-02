<?php require_once( '../../loadBase.php' ); ?>
<html xmlns:php="http://www.neoconn.com/namespaces/php" version="1.0">

	<head>
		<title>descolando!</title>
		
		<script type="text/javascript" src="/base0.9/library/js/web/Literal.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/Label.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/TextBox.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/TextArea.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/CheckBox.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/RadioButton.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/RadioButton.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/Password.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/Form.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/FormImage.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/DropDownList.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/ListBox.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/form/Reset.js"></script>
		<script type="text/javascript" src="/base0.9/library/js/web/Button.js"></script>
		
		
		<script type="text/javascript">
		
			addFunctionSinc(arrayCmbs)
			{
				var j = arrayCmbs.length;
				for(var i = 0; i<j; i++)
				{
					arrayCmbs[i].click = Call_Sinc;
				}
			}
		
			var cmbSincs = ['','','','','','','',''];
			
			
			//1 - TextBox
			changeLabelTextbox = function()
			{
				$C('label_1').setText($C('textbox_1').get('value'));
			};
			
			resetTextbox = function()
			{
				$C('label_1').setText('Label para TextBox:');
				$C('textbox_1').set('value','some value');
			};
			
			//2 - TextArea
			changeLabelTextarea = function()
			{
				$C('label_2').setText($C('textarea_2').get('value'));
			};
			
			resetTextarea = function()
			{
				$C('label_2').setText('Label para TextBox:');
				$C('textarea_2').set('value','some value');
			};
			
			//3 - CheckBox
			checkAll = function()
			{
				$C('checkbox_3_1').set('checked',true);
				$C('label_3_1').set('text','checked');
				
				$C('checkbox_3_2').set('checked',true);
				$C('label_3_2').set('text','checked');
			};
			
			uncheckAll = function()
			{
				$C('checkbox_3_1').set('checked',false);
				$C('label_3_1').set('text','unchecked');
				
				$C('checkbox_3_2').set('checked',false);
				$C('label_3_2').set('text','unchecked');			
			};
			
			//4 - RadioButton
			switchRadio = function()
			{
				if( $C('radiobutton_4_1').get('checked') == true || $C('radiobutton_4_1').get('checked') == 'checked' )
				{
					$C('radiobutton_4_2').set('checked',true);
				}
				else
				{
					if( $C('radiobutton_4_2').get('checked') == true || $C('radiobutton_4_2').get('checked') == 'checked' )
					{
						$C('radiobutton_4_1').set('checked',true);
					}
				}
			};
			
			//5 - DropDownList
			addItemToDropDown = function()
			{
				var now = new Date();
				var numOpt = $C('dropDownList_5').realElement.options.length;
				var newOpt = new OptionItem();
				newOpt.set('id','OPTION_ITEM_FOR_DDL_' + now.getHours() + now.getMinutes() + now.getSeconds() + numOpt);
				newOpt.set('value','VALUE_' + $C('textbox_5').get('value'));
				newOpt.set('text',$C('textbox_5').get('value'));
				
				$C('dropDownList_5').addChild(newOpt);
				newOpt.set('selected',true);
				$C('textbox_5').set('value','');
				$('textbox_5').focus();
			};
			
			changeItemFromDropDown = function()
			{
				var optSelected = $C('dropDownList_5').getSelectedOption();
				if(optSelected)
				{
					optSelected.set('text',$C('textbox_5').get('value'));
				}
				$C('textbox_5').set('value','');
				$('textbox_5').focus();				
			};
			
			removeItemFromDropDown = function()
			{
				var optSelected = $C('dropDownList_5').getSelectedOption();
				$C('dropDownList_5').removeChild(optSelected);
			};
			

			//6 - ListBox
			addItemToListBox = function()
			{
				var now = new Date();
				var numOpt = $C('listBox_6').realElement.options.length;
				var newOpt = new OptionItem();
				newOpt.set('id','OPTION_ITEM_FOR_LISTBOX_UNIC_' + now.getHours() + now.getMinutes() + now.getSeconds() + numOpt);
				newOpt.set('value','VALUE_' + $C('textbox_6').get('value'));
				newOpt.set('text',$C('textbox_6').get('value'));
				
				$C('listBox_6').addChild(newOpt);
				newOpt.set('selected',true);
				$C('textbox_6').set('value','');
				$('textbox_6').focus();
			};
			
			changeItemFromListBox = function()
			{
				var optSelected = $C('listBox_6').getSelectedOption();
				if(optSelected)
				{
					optSelected.set('text',$C('textbox_6').get('value'));
				}
				$C('textbox_6').set('value','');
				$('textbox_6').focus();				
			};
			
			removeItemFromListBox = function()
			{
				var optSelected = $C('listBox_6').getSelectedOption();
				$C('listBox_6').removeChild(optSelected);
			};
			
			//7 - ListBox (Seleção múltipla)
			
			//8 - PasswordField
			changeLabelPass = function()
			{
				$C('passwordfield_8').setText($C('').get('value'));
			};
			resetPass = function()
			{
				$C('label_8').setText('PasswordField:');
				$C('passwordfield_8').set('value','123');
			};
			
		</script>
		<style type="text/css">
			table th
			{
				border:1px solid #646464;
			}
			table td
			{
				border:1px solid #646464;
				text-align:center;
			}
		</style>
	</head>	
<body>

	<php:Form id="frm" runat="server">
		<table style="border:1px solid #646464; width:100%">
			<thead>
				<tr>
					<th>
						Componente
					</th>
					<th>
						Servidor
					</th>
					<th>
						Local
					</th>					
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width:50%">
						<php:Label id="label_1" for="textbox_1" runat="server">Label para TextBox:</php:Label>
						<br />
						<php:TextBox id="textbox_1" runat="server" value="some value"/>						
					</td>
					<td style="width:25%">
						<php:Button id="buttonServidor_1" runat="server">Mudar Label</php:Button><br />
						<php:Button id="buttonServidor_1_reset" runat="server">Resetar</php:Button>
					</td>
					<td>
						<php:Button id="buttonCliente_1" onclick="changeLabelTextbox()" runat="server">Mudar Label</php:Button>
						<br />
						<php:Button id="buttonCliente_1_reset" onclick="resetTextbox()" runat="server">Resetar</php:Button><br />
						<button id="cmbSinc_1">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:Label id="label_2" for="textarea_2" runat="server">Label para TextArea:</php:Label>
						<br />
						<php:TextArea id="textarea_2" runat="server"></php:TextArea>						
					</td>
					<td>
						<php:Button id="buttonServidor_2" runat="server">Mudar Label</php:Button>
						<br />
						<php:Button id="buttonServidor_2_reset" runat="server">Resetar</php:Button>						
					</td>
					<td>
						<php:Button id="buttonCliente_2" onclick="changeLabelTextarea()" runat="server">Mudar Label</php:Button>
						<br />
						<php:Button id="buttonCliente_2_reset" onclick="resetTextarea()" runat="server">Resetar</php:Button>
						<br />
						<button id="cmbSinc_2">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:CheckBox id="checkbox_3_1" checked="checked" runat="server" />
						<php:Label id="label_3_1" for="checkbox_3_1" runat="server">checked</php:Label>
						<br />
						<php:CheckBox id="checkbox_3_2" runat="server" />
						<php:Label id="label_3_2" for="checkbox_3_2" runat="server">unchecked</php:Label>												
					</td>
					<td>
						<php:Button id="buttonServidor_3_1" runat="server">Check All</php:Button><br />
						<php:Button id="buttonServidor_3_2" runat="server">Uncheck All</php:Button>
					</td>
					<td>
						<php:Button id="buttonCliente_3_1" onclick="checkAll()" runat="server">Check All</php:Button>
						<br />
						<php:Button id="buttonCliente_3_2" onclick="uncheckAll()" runat="server">Uncheck All</php:Button>
						<br />
						<button id="cmbSinc_3">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:RadioButton id="radiobutton_4_1" name="radiobutton" checked="checked" runat="server" />
						<php:Label id="label_4_1" for="radiobutton_4_1" runat="server">checked</php:Label>
						<br />
						<php:RadioButton id="radiobutton_4_2" name="radiobutton" runat="server" />
						<php:Label id="label_4_2" for="radiobutton_4_2" runat="server">unchecked</php:Label>												
					</td>
					<td>
						<php:Button id="buttonServidor_4" runat="server">switch</php:Button>
					</td>
					<td>
						<php:Button id="buttonCliente_4" onclick="switchRadio()" runat="server">switch</php:Button>
						<br />
						<button id="cmbSinc_4">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:Label id="label_5_1" for="dropDownList_5" runat="server">DropDownList:</php:Label><br />					
						<php:DropDownList id="dropDownList_5" runat="server">
							<php:OptionItem id="opt_for_dropdown_1" runat="server">Opção 1</php:OptionItem>
						</php:DropDownList>
						
						<br />
						<php:Label id="label_5_2" for="textbox_5" runat="server">Valor:</php:Label> <label title="Digite um valor abaixo e utilize algum comando nos botões ao lado">[?]</label>
						<php:TextBox id="textbox_5" runat="server" />																		
					</td>
					<td>
						<php:Button id="buttonServidor_5_Add" runat="server">Add</php:Button><br />
						<php:Button id="buttonServidor_5_Del" runat="server">Del</php:Button><br />
						<php:Button id="buttonServidor_5_Chg" runat="server">Change</php:Button><br />
					</td> 					
					<td>
						<php:Button id="buttonCliente_5_Add" onclick="addItemToDropDown()" runat="server">Add</php:Button><br />
						<php:Button id="buttonCliente_5_Del" onclick="removeItemFromDropDown()" runat="server">Del</php:Button><br />
						<php:Button id="buttonCliente_5_Chg" onclick="changeItemFromDropDown()" runat="server">Change</php:Button><br />
						
						<button id="cmbSinc_5">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:Label id="label_6_1" for="listBox_6" runat="server">ListBox (único):</php:Label><br />				
						<php:ListBox id="listBox_6" size="5" runat="server">
							<php:OptionItem id="opt_for_listbox_unic_1" runat="server">Escolha 1</php:OptionItem>
							<php:OptionItem id="opt_for_listbox_unic_2" runat="server">Escolha 2</php:OptionItem>
							<php:OptionItem id="opt_for_listbox_unic_3" runat="server">Escolha 3</php:OptionItem>
						</php:ListBox>
						
						<br />
						<php:Label id="label_6_2" for="textbox_6" runat="server">Valor:</php:Label> <label title="Digite um valor abaixo e utilize algum comando nos botões ao lado">[?]</label>
						<php:TextBox id="textbox_6" runat="server" />																		
					</td>
					<td>
						<php:Button id="buttonServidor_6_Add" runat="server">Add</php:Button><br />
						<php:Button id="buttonServidor_6_Del" runat="server">Del</php:Button><br />
						<php:Button id="buttonServidor_6_Chg" runat="server">Change</php:Button><br />
					</td> 					
					<td>
						<php:Button id="buttonCliente_6_Add" onclick="addItemToListBox()" runat="server">Add</php:Button><br />
						<php:Button id="buttonCliente_6_Del" onclick="removeItemFromListBox()" runat="server">Del</php:Button><br />
						<php:Button id="buttonCliente_6_Chg" onclick="changeItemFromListBox()" runat="server">Change</php:Button><br />
						
						<button id="cmbSinc_6">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:Label id="label_7_1" for="listBox_7" runat="server">ListBox (múltiplo):</php:Label><br />					
						<php:ListBox id="listBox_7" multiple="multiple" size="5" runat="server">
							<php:OptionItem id="opt_for_listbox_mult_1" selected="selected" runat="server">Escolha 1</php:OptionItem>
							<php:OptionItem id="opt_for_listbox_mult_2" runat="server">Escolha 2</php:OptionItem>
							<php:OptionItem id="opt_for_listbox_mult_3" selected="selected" runat="server">Escolha 3</php:OptionItem>
						</php:ListBox>
						
						<br />
						<php:Label id="label_7_2" for="textbox_7" runat="server">Valor:</php:Label> <label title="Digite um valor abaixo e utilize algum comando nos botões ao lado">[?]</label>
						<php:TextBox id="textbox_7" runat="server" />																		
					</td>
					<td>
						<php:Button id="buttonServidor_7_Add" runat="server">Add</php:Button><br />
						<php:Button id="buttonServidor_7_Del" runat="server">Del</php:Button><br />
						<php:Button id="buttonServidor_7_Chg" runat="server">Change</php:Button><br />
					</td> 					
					<td>
						<php:Button id="buttonCliente_7_Add" runat="server">Add</php:Button><br />
						<php:Button id="buttonCliente_7_Del" runat="server">Del</php:Button><br />
						<php:Button id="buttonCliente_7_Chg" runat="server">Change</php:Button><br />
						
						<button id="cmbSinc_7">sinc</button>
					</td>					
				</tr>
				<tr>
					<td>
						<php:Label id="label_8" for="passwordfield_8" runat="server">PasswordField:</php:Label>
						<br />
						<php:PasswordField id="passwordfield_8" runat="server" value="123"/>					
					</td>
					<td>
						<php:Button id="buttonServidor_8" runat="server">Mudar Label</php:Button>
						<br />
						<php:Button id="buttonServidor_8_reset" runat="server">Resetar</php:Button>
					</td> 					
					<td>
						<php:Button id="buttonCliente_8" onclick="changeLabelPass" runat="server">Mudar Label</php:Button><br />
						<br />
						<php:Button id="buttonCliente_8_reset" onclick="resetPass" runat="server">Resetar</php:Button><br />
						
						<button id="cmbSinc_8">sinc</button>
					</td>					
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td>
						<php:FormImage id="formImageSubmit" runat="server" />
					</td>
					<td>
						<php:Reset id="inputReset" runat="server" value="Reset" />
					</td>
				</tr>		
			</tfoot>
		</table>
	</php:Form>
	
</body>
</html>