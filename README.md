jsGlobals
=========

	
	An utility class to send the desired globals from PHP to Javascript.
	
	Imagine that you have this JavaScript code, that saves a note for a contact of a customer.
	
	function saveNota() {
		new Ajax.Request('dispatcher.php', {
			method:'post',
			parameters: {action: $('id_box').data('id') ? UPDATE : CREATE,
						object: NOTES,
						id: $('id_box').data('id'),
						customer: numCustomerID,
						contact: numContactID,
						text: $('nota_box').text()},
			onSuccess: function(transport){
				// doSomething
			}
		});
	}
	
	Both numCustomerID and numContactID values come originally from backend. 
	The easiest way to have them availabile in your page is to use hidden fields.
	
	Another way is to set a couple of global vars containing the same information.
	Using this class is as simple as adding this to your code:
	
	<script>
<?php
	$objJS = new jsGlobalsClass();

	$objJS->numContactID = $numRecordID;
	$objJS->numCustomerID = $numCustomerID;

	echo $objJS;
?>
	</script>
	
	that will add this to your generated html:
	<script>
		var numCustomerID = 998,
    		numContactID = 2323;
	</script>
		
	But what if you need to pass intl' strings because your product is multilingual.
	Or an array to fill a menu.

	<script>
<?php
	$objJS = new jsGlobalsClass();

	$objJS->order();

	// some numbers
	$objJS->numContactID = $numRecordID;
	$objJS->numCustomerID = $numCustomerID;

	// an array to dinamically create a select
	global $gArrDirecciones;
	$objJS->arrDirs = array();
	foreach($gArrDirecciones as $key => $label){
		$objJS->arrDirs = array('ID' => $key, 'LABEL' => $label);
	}
	
	// some int'l texts
	$objJS->textHelp = $objJS->text($TEXTOS['texto_help']);
	$objJS->textConfirmEdit = $objJS->text($TEXTOS['text_confirm_edit']);
	$objJS->textConfirmDelete = $objJS->text($TEXTOS['text_confirm_delete']);
	$objJS->reglaNombre = '"required,nombre,' . $ERROR['rsv_nombre'] . '"';

	// the setup for the panels in the page 
	$objJS->arrPanels = array('proyectos', 'direcciones', 'notas');
	$objJS->strNombrePanel = $objJS->text($objJS->arrPanels[$objJS->numCurPanel]);


	echo $objJS;
?>
	</script>
	
	The code above will generate this javascript.
	<script>
		var arrDirs = {"0":{"ID":"1","LABEL":"Sede central"},"1":{"ID":"2","LABEL":"Envíos"},"2":{"ID":"3","LABEL":"Facturación"},"3":{"ID":"4","LABEL":"Sucursal"}},
    		arrPaneles = {"0":"proyectos","1":"direcciones","2":"notas"},
		    numContactID = 2323,
    		numCustomerID = 998,
    		numCurPanel = 0,
		    reglaNombre = "required,nombre,El campo nombre es obligatorio.",
    		strNombrePanel = "proyectos",
    		textoAyuda = "Sit&uacute;e el cursor sobre el icono, bot&oacute;n o campo para el que desee obtener ayuda.",
    		textoConfirmDelete = "\u277Est\u341 seguro que desea eliminar este [[1]]?",
    		textoConfirmEdit = "Est\u341 editando un [[1]].\n\u277Est\u341 seguro de que quiere cancelar la edici\u363n y mostrar otro en su lugar?";
	</script>

	So you are sending, easily, PHP data to Javascript.
