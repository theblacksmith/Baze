function tryKeyEvent( evt )
{
	alert( evt.keyCode );
	if( evt.keyCode==46 )
	{
		if( confirmDelete( ) )
		{
			alert( 'reconheceu a tecla delete' );
		}
	}
}
function confirmDelete( )
{
	return confirm ("Tem Certeza de que deseja excluir os arquivos selecionados?");
}

function iconSelect ( icon )
{
	textArea = document.getElementById( icon.id + "_icon_textarea" );

	if ( textArea.style.backgroundColor == 'rgb(204, 204, 204)' )
	{
		textArea.style.backgroundColor = "#FFFFFF";
	}
	else
	{
		textArea.style.backgroundColor = "#CCCCCC";
	}
}