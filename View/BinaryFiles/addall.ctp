<script>
  function readablizeBytes(bytes) {
	var s = ['bytes', 'kb', 'MB', 'GB', 'TB', 'PB'];
	var e = Math.floor(Math.log(bytes)/Math.log(1024));
	return (bytes/Math.pow(1024, Math.floor(e))).toFixed(2)+" "+s[e];
  }

  function handleFileSelect(evt) {
    var files = evt.target.files; // FileList object

    // files is a FileList of File objects. List some properties.
    var output = [];
    for (var i = 0, f; f = files[i]; i++) {
      output.push('<li><strong>', f.name, '</strong> (', f.type || 'n/a', ') - ',
    		  readablizeBytes(f.size), '</li>');
    }
    document.getElementById('list').innerHTML = '<ul>' + output.join('') + '</ul>';
  }

  document.getElementById('fileupload').addEventListener('change', handleFileSelect, false);
</script>

<?php
	function let_to_num($v){ //This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
	    $l = substr($v, -1);
	    $ret = substr($v, 0, -1);
	    switch(strtoupper($l)){
	    case 'P':
	        $ret *= 1024;
	    case 'T':
	        $ret *= 1024;
	    case 'G':
	        $ret *= 1024;
	    case 'M':
	        $ret *= 1024;
	    case 'K':
	        $ret *= 1024;
	        break;
	    }
	    return $ret;
	}

	$max_file_uploads = ini_get('max_file_uploads');
	$upload_max_filesize = ini_get('upload_max_filesize');
?>
<div class="documents form">
<?php echo $this->Form->create('Document', array('type' => 'file', 'enctype' => 'multipart/form-data'));?>
	<fieldset>
		<legend><?php echo sprintf('Añadir varios ficheros al Proyecto %s', $project['Project']['name']); ?></legend>
	<?php
		//echo $this->Form->file("filenames[]", array('label' => 'Fichero', 'multiple' => true));
		echo $this->Form->input('filenames', array('id' => 'fileupload',
				'name' => 'data[Document][filenames][]',
				'multiple' => 'true',
				'type' => 'file', 
				'label' => __('Ficheros'),
				'title' => __('Puedes subir varios ficheros de una sola vez. Se puede demorar unos minutos.')
		));
		echo "<output id='list'></output><br />";
		echo $this->Html->tag("p", __('Puedes seleccionar varios ficheros usando la tecla Control', true));
		echo $this->Html->tag("p", sprintf(__('N maximo de ficheros por envio: %s'), ini_get('max_file_uploads')));		
		echo $this->Html->tag("p", sprintf(__('Tamano maximo por fichero: %s'), ini_get('upload_max_filesize')));

		$label = $this->Html->tag('span', __('Traduccion automatica'), array('title' => __('Podria demorarse varios minutos')));
		echo $this->Form->input('autotranslate', array('type' => 'checkbox', 'label' => $label));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>