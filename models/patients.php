<?php

	$whereCluase = array();
	$replacements = array();
	$letter = false;
	$isSingle = false;

// ESTE ES CUANDO VENGO DE CREAR UN TURNO
	if( ( $newPatient = __GETField( 'id' ) ) && __validateID( $newPatient ) ) {
		$isSingle = true;
		$whereCluase[] = ' p.id = ? ';
		$replacements[] = $newPatient;
		
	} else {
// ESTO ES CUANDO ESTOY ASI: pacientes/listar-por-letra/B SI FUERA EL CASO,
// CASO CONTRARIO LISTO LOS APELLIDO QUE EMPIECEN CON 'A'
		$letter = $g_router->seg( 3 ) ?: 'A';
		$whereCluase[] = ' p.apellidos LIKE ?';
		$replacements[] = $letter . '%';
	}

// ESTAS VARIABLES SON LAS QUE SE USAN EL VIEW
	$username = __getUsername();
	// veo si tengo que paginar
	$offset = __validateID( __GETField( 'pagina' ) );
	if( !$offset ) {
		$offset = 0;
	}
	// pido los pacientes en base a un $offset
	$patients = q_getPatients( $whereCluase, $replacements, $offset );
	// veo si tengo que SEGUIR paginar
	if( count( $patients ) == 21 ) {
		array_pop( $patients );
		$stillMorePages = true;
	} else {
		$stillMorePages = false;
	}
	
	$removeSuccess = false;
	$removeError = false;
	$editError = false;
	if( __issetGETField( 'exito', 'borrar-paciente' ) ) {
		$removeSuccess = true;
	// ... o de error
	} else if( __issetGETField( 'error', 'borrar-paciente' ) ) {
		$removeError = true;
	} else if( __issetGETField( 'error', 'editar-paciente' ) ) {
		$editError = true;
	}

// LOAD THE VIEW
	__render( 
		'patients', 
		array(
			'username' => $username,
			'patients' => $patients,
			'removeSuccess' => $removeSuccess,
			'removeError' => $removeError,
			'editError' => $editError,
			'letter' => $letter,
			'stillMorePages' => $stillMorePages,
			'offset' => $offset,
			'isSingle' => $isSingle
		)
	);

?>
