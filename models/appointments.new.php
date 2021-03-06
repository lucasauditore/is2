<?php

	if( __issetPOST( array( 'fecha', 'hora', 'idMedico', 'idPaciente' ) ) ) {
		$date = __toISODate( $_POST['fecha'] );
		$time = __toISOTime( $_POST['hora'] );
		$doctorID = __validateID( $_POST['idMedico'] );
		$patientID = __validateID( $_POST['idPaciente'] );
		
		if( !$date || !$time || !$doctorID || !$patientID ) {
			__redirect( '/turnos/crear?error=crear-turno' );
		}
		
		// debo hacer un par de comprobaciones
		// 1) no puedo crear un turno donde el medico
		// no este disponible para ese dia y hora
		$day = date( 'N', strtotime( $date ) );
		$res = q_checkDoctorAvailability( array( $doctorID, $time, $time, $day ) );
		// el medico no tiene tal horario
		if( !count( $res ) ) {
			__redirect( '/turnos/crear?error=crear-turno' );
		}
		
		// 2) no puedo crear un turno mayor a 7 dias desde el dia actual
		$diff = date_diff( date_create(), date_create( $date ) )->format( '%d' );
		if( $diff < 0 || $diff > 7 ) {
			__redirect( '/turnos/crear?error=crear-turno' );
		}
		
		$insertId = $g_db->insert(
			'
				INSERT INTO 
					turnos
				VALUES
					( null, ?, ?, ?, ?, ? )
			',
			array( $date, $time, $doctorID, $patientID, 'esperando' )
		);
		
		if( !$insertId ) {
			__redirect( '/turnos/crear?error=crear-turno' );
		}
		
		__redirect( '/turnos?id=' . $insertId );
	}

// PIDO LA LISTA DE DOCTORES
	$doctors = q_getAllDoctors();

// TODAS ESTAS SON VARIABLES QUE DEBEN USARSE EN LA VIEW //
	$username = __getUsername();
	
// VENGO DE UN $_POST PERO HUBO PROBLEMAS
	if( __GETField( 'error' ) ) {
		$createError = true;
	} else {
		$createError = false;
	}
	
	$wantedDate = ( $wantedDate = __GETField( 'fecha' ) ) && __toISODate( $wantedDate ) ? $wantedDate : false;
	
// LOAD THE VIEW
	__render( 
		'appointments.new', 
		array(
			'username' => $username,
			'createError' => $createError,
			'doctors' => $doctors,
			'wantedDate' => $wantedDate
		)
	);
	
?>