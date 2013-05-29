<?php

// ESTA PARTE ES CUANDO SE ESTA FILTRANDO LOS TURNOS MEDIANTE LAS COLUMNAS DE LA GRID
	$orderByClause = array();
	$isOrderByTime = false;
	$isOrderByDate = false;
	// usada para mostrar turnos segun su estado
	$statusValue = false;
	// me fijo si la grid esta siendo ordenado por algun tipo de orden
	if( __issetGETField( 'fecha', 'desc' ) ) {
		$orderByClause[] = 't.fecha DESC';
		$isOrderByDate = true;

	} else if( __issetGETField( 'hora', 'desc' ) ) {
		$orderByClause[]  = 't.hora DESC';
		$isOrderByTime = true;

	} else if( __issetGETField( 'medico', 'asc' ) ) {
		$orderByClause[] = 'm.apellidos ASC';
	} else if( __issetGETField( 'medico', 'desc' ) ) {
		$orderByClause[] = 'm.apellidos DESC';
		
	} else if( __issetGETField( 'paciente', 'asc' ) ) {
		$orderByClause[] = 'm.apellidos ASC';
	} else if( __issetGETField( 'paciente', 'desc' ) ) {
		$orderByClause[] = 'p.apellidos DESC';
	
	} else if( __issetGETField( 'estado', 'confirmados' ) ) {
		$statusValue = 'confirmados';
	} else if( __issetGETField( 'estado', 'cancelados' ) ) {
		$statusValue = 'cancelados';
	}
	// siempre se debe ordernar por fecha SOLO SI NO SE ESTA ORDENANDO POR ALGO
	if( !$isOrderByDate && !count( $orderByClause ) ) {
		$orderByClause[] = ' t.fecha ASC ';
	}
	// siempre se debe ordernar por ahora SI O SI
	if( !$isOrderByTime ) {
		$orderByClause[] = ' t.hora ASC ';
	}
	
// ACA CONSTRUYO EL WHERE DE MI QUERY
	$replacements = array();
	$whereClause = array();
	$isLimitClause = false;
	// aca voy a guardar los parametros de busqueda (fecha, hora, etc)
	// para luego utilizarlo para rellenar el formulario de busqueda
	$persistValues = array(
		'fromDate' => '',
		'toDate' => '',
		'fromTime' => '',
		'toTime' => '',
		'doctorsList' => array(),
		'patientsDNI' => '',
		'status' => ''
	);
	$isSearch = false;
	$isQuickSearch = false;
	$quickSearchValue = false;

// ESTO ES CUANDO EL USUARIO HA HECHO CLICK EN EL BOTON BUSCAR
	if( ( $search = __GETField( 'busqueda-avanzada' ) ) ) {
		$isSearch = true;
	
		$searchParts = explode( '|', base64_decode( $search ) );
		if( count( $searchParts ) != 5 ) {
			__redirect( '/turnos?error=buscar-turno' );
		}
		// la primera parte es la feha
		$dateRange = explode( '@', $searchParts[0] );
		// no pasa nada si se mete mano en estos datos
		if( isset( $dateRange[0] ) && $dateRange[0] ) {
			$whereClause[] = ' t.fecha >= ? ';
			$replacements[] = $persistValues['fromDate'] = $dateRange[0];
		}
		if( isset( $dateRange[1] ) && $dateRange[1] ) {
			$whereClause[] = ' t.fecha <= ? ';
			$replacements[] = $persistValues['toDate'] = $dateRange[1];
		}
		// la segunda parte el time range
		$timeRange = explode( '@', $searchParts[1] );
		if( isset( $timeRange[0] ) && $timeRange[0] ) {
			$whereClause[] = ' t.hora >= ? ';
			$replacements[] = $persistValues['fromTime'] = $timeRange[0];
		}
		if( isset( $timeRange[1] ) && $timeRange[1] ) {
			$whereClause[] = ' t.hora <= ? ';
			$replacements[] = $persistValues['toTime'] = $timeRange[1];
		}
		// la tercera parte es una lista de doctores
		$doctorsLists = explode( '-', $searchParts[2] );
		// hago el OR clause
		$doctorsOrClause = array();
		foreach( $doctorsLists as $doctorID ) {
			if( $doctorID ) {
				$doctorsOrClause[] = ' m.id = ? ';
				$replacements[] = $persistValues['doctorsList'][$doctorID] = $doctorID;
			}
		}
		if( count( $doctorsOrClause ) > 0 ) {
			$whereClause[] = ' ( ' . implode( ' OR ', $doctorsOrClause ) . ' ) ';
		}
		// la cuarta parte es una lista de pacientes de DNIs
		$patientsDNI = explode( '-', $searchParts[3] );
		// hago el OR clause
		$patientsOrClause = array();
		foreach( $patientsDNI as $dni ) {
			if( $dni ) {
				$patientsOrClause[] = ' p.dni = ? ';
				$replacements[] = $persistValues['patientsDNI'][] = $dni;
			}
		}
		if( count( $patientsOrClause ) > 0 ) {
			$whereClause[]	= ' ( ' . implode( ' OR ', $patientsOrClause ) . ' ) ';
			$persistValues['patientsDNI'] = implode( ' ', $persistValues['patientsDNI'] );
		}
		// la quinta parte es el estado del turno
		if( $searchParts[4] ) {
			$statusValue = $persistValues['status'] = $searchParts[4];
		};
		
		if( !count( $whereClause ) ) {
			$whereClause[] = ' 1 = 1 ';
		}
	
// ESTE ES CUANDO VENGO DE CREAR UN TURNO
	} else if( ( $newAppointment = __GETField( 'id' ) ) && __validateID( $newAppointment ) ) {
		$isSearch = true;
	
		$whereClause[] = ' t.id = ? ';
		$replacements[] = $newAppointment;
		
// ACA CUANDO HACE UNA BUSQUEDA RAPIDA
	} else if( ( $keyword = __GETField( 'busqueda' ) ) ) {
		$keyword = __sanitizeValue( base64_decode( $keyword ) );
		if( !$keyword ) {
			__redirect( '/turnos?error=buscar-turno-rapido' );
		}
		$keyword = explode( '=', $keyword );
		if( count( $keyword ) != 2 ) {
			__redirect( '/turnos?error=buscar-turno-rapido' );
		}
		
		$field =  $keyword[0];
		$value = $keyword[1];
		if( $field == 'fecha' ) {
			$whereClause[] = ' t.fecha = ? ';
			$replacements[] = $value;
			
		} else if( $field == 'hora' ) {
			$whereClause[] = ' t.hora = ? ';
			$replacements[] = $value;
			
		} else if( $field == 'estado' ) {
			$whereClause[] = ' t.estado = ? ';
			$replacements[] = $value;
			
		} else if( $field == 'comodin' ) {
			$whereClause[] = ' ( m.nombres LIKE ? OR m.apellidos LIKE ? OR p.apellidos LIKE ? OR p.nombres LIKE ? ) ';
			$replacements[] = '%' . $value . '%';
			$replacements[] = '%' . $value . '%';
			$replacements[] = '%' . $value . '%';
			$replacements[] = '%' . $value . '%';
		
		} else {
			__redirect( '/turnos?error=buscar-turno-rapido' );
		}
		
		$quickSearchValue = $value;
		$isQuickSearch = true;
		// this last two token are for the limit clause
		$isLimitClause = true;
	
// ESTE ES EL WHERE NORMAL, OSEA CUANDO SE ESTA ACCEDIENDO DIRECTAMENTE A /turnos
	} else {
		$whereClause[] = ' t.fecha >= ? ';
		$replacements[] = date( 'Y-m-d' );
		$whereClause[] = ' t.fecha <= ? ';
		// en modo normal se muestran los turnos desde la fecha actual hasta + 7 dias
		$replacements[] = date( 'Y-m-d', strtotime( '+7 days' ) );
	}
	
	// SE PUEDEN FILTAR LOS TURNOS MEDIANTE LA COLUMNA "ACCIONES" O MEDINATE UNA BUSQUEDA
	if( $statusValue && ( $statusValue = __getAppointmentStatus( $statusValue ) ) ) {
		$whereClause[] = ' t.estado = ? ';
		$replacements[] = $statusValue;
	}
	
	$appointments = $g_db->select(
		'
			SELECT 
				t.id, t.fecha, t.hora, t.estado,
				m.nombres AS medicoNombres, m.apellidos AS medicoApellidos,
				p.nombres AS pacienteNombres, p.apellidos AS pacienteApellidos
			FROM turnos AS t 
				INNER JOIN medicos AS m 
					ON m.id = t.idMedico 
				INNER JOIN pacientes AS p 
					ON p.id = t.idPaciente 
			WHERE ' .
				implode( ' AND ', $whereClause ) .
				
			'ORDER BY ' .
				implode( ' , ', $orderByClause ) .
				
			( $isLimitClause ? ' LIMIT 0, 21 ' : '' )
		,
		$replacements
	);
	
	// too much records
	if( $isQuickSearch && count( $appointments ) == 21 ) {
		array_pop( $appointments );
		$tooMuchRecords = true;
	} else {
		$tooMuchRecords = false;
	}

// PIDO LOS MEDICOS, ESTOS ES DEBIDO A QUE LA BUSQUEDA DESPLIEGA UNA
// LISTA QUE CONTIENE TODOS LOS MEDICOS EN EL SISTEMA
	$doctors = q_getAllDoctors();
	
// TODAS ESTAS SON VARIABLES QUE DEBEN USARSE EN LA VIEW //
	$username = __getUsername();
	
	$confirmSuccess = false;
	$confirmError = false;
	$cancelSuccess = false;
	$cancelError = false;
	$removeSuccess = false;
	$removeError = false;
	$resetSuccess = false;
	$resetError = false;
	$searchError = false;
	$searchQuickError = false;
	// aca veo si vengo de un confirmar-turno, el cual fue ok
	// con esto mostrare los respectivos mensajes de exito...
	if( __issetGETField( 'exito', 'confirmar-turno' ) ) {
		$confirmSuccess = true;
	// ... o de error
	} else if( __issetGETField( 'error', 'confirmar-turno' ) ) {
		$confirmError = true;
	
	} else if( __issetGETField( 'exito', 'cancelar-turno' ) ) {
		$cancelSuccess = true;
	} else if( __issetGETField( 'error', 'cancelar-turno' ) ) {
		$cancelError = true;
	
	} else if( __issetGETField( 'exito', 'borrar-turno' ) ) {
		$removeSuccess = true;
	} else if( __issetGETField( 'error', 'borrar-turno' ) ) {
		$removeError = true;
	
	} else if( __issetGETField( 'exito', 'reiniciar-turno' ) ) {
		$resetSuccess = true;
	} else if( __issetGETField( 'error', 'reiniciar-turno' ) ) {
		$resetError = true;
	
	} else if( __issetGETField( 'error', 'buscar-turno' ) ) {
		$searchError = true;
		$isSearch = true;
	} else if( __issetGETField( 'error', 'buscar-turno-rapido' ) ) {
		$searchQuickError = true;
	}
	
	// esto es usado para mostrar un mensaje cuando se accede a /turnos
	// sobre de que los turnos que estan siendo mostrados son los desde la
	// la fecha actual hasta los siguiente 7 dias
	$currentDate = !$isSearch && !$isQuickSearch ? date( 'd/m/Y' ) : false;
	
// LOAD THE VIEW
	__render( 
		'appointments', 
		array(
			'username' => $username,
			'currentDate' => $currentDate,
			'confirmSuccess' => $confirmSuccess,
			'confirmError' => $confirmError,
			'cancelSuccess' => $cancelSuccess,
			'cancelError' => $cancelError,
			'removeSuccess' =>$removeSuccess,
			'removeError' => $removeError,
			'resetSuccess' => $resetSuccess,
			'resetError' => $resetError,
			'searchError' => $searchError,
			'searchQuickError' => $searchQuickError,
			'quickSearchValue' => $quickSearchValue,
			'tooMuchRecords' => $tooMuchRecords,
			'appointments' => $appointments,
			'doctors' => $doctors,
			'persistValues' => $persistValues
		)
	);

?>
